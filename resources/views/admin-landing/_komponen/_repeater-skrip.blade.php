<script>
/**
 * Repeater handler generik untuk sub-menu PPDB (persyaratan, tahapan,
 * jadwal, faq). Dipanggil dengan konfigurasi per-halaman:
 *
 *   lpRep.init({
 *       listId: 'lpReqList',
 *       addBtnId: 'lpReqAddBtn',
 *       saveBtnId: 'lpReqSaveAll',
 *       emptyId: 'lpReqEmpty',
 *       storeUrl: '...',
 *       updateUrlTpl: '.../{id}',
 *       cardClass: 'lp-req-card',     // class untuk tiap baris
 *       removeBtnSelector: '[data-role="remove"], .btn-remove-row',
 *       gatherPayload: function(rowEl) { return { fd, isNew } | null },
 *       wysiwyg: true | false,
 *       wysiwygToolbar: [...],
 *       wysiwygPlaceholder: '...',
 *   });
 *
 * Setiap baris existing dari server sudah di-render dengan `data-id` (kalau
 * ada) dan field prefill. Baris baru di-clone dari `<template>` di view.
 */
window.lpRep = (function () {
    function getCsrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function notify(opts) {
        if (typeof Swal === 'undefined' || !Swal || !Swal.fire) {
            try { window.alert((opts.title || '') + ' ' + (opts.text || '')); } catch (e) {}
            return Promise.resolve({ isConfirmed: true });
        }
        // Notifikasi standar: toast pojok kanan atas.
        // Kalau caller tidak set toast/position, pakai default ringkas.
        var isAction = !!(opts.showCancelButton || opts.showConfirmButton || opts.input);
        if (isAction) {
            return Swal.fire(opts);
        }
        var cfg = Object.assign({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2200,
            timerProgressBar: true,
        }, opts || {});
        return Swal.fire(cfg);
    }

    function htmlListToItems(html) {
        var tmp = document.createElement('div');
        tmp.innerHTML = html || '';
        var items = [];
        tmp.querySelectorAll('li').forEach(function (li) {
            var text = (li.textContent || '').replace(/\s+/g, ' ').trim();
            if (text) items.push(text);
        });
        if (!items.length) {
            var plain = (tmp.textContent || '').replace(/\s+/g, ' ').trim();
            if (plain) items.push(plain);
        }
        return items;
    }

    function initQuillIn(card, opts) {
        if (!opts.wysiwyg || typeof Quill === 'undefined') return;
        var editorEl = card.querySelector('[data-role="editor"]');
        var inputEl = card.querySelector('[data-role="items-input"]');
        if (!editorEl || !inputEl) return;
        if (editorEl.__quill) return;

        // Quill butuh dimensi visible (offsetWidth/offsetHeight) saat inisialisasi.
        // Kalau card dalam keadaan collapsed (grid-template-rows: 0fr, tinggi 0),
        // Quill error / blank / tidak bisa menerima input. Buka dulu card,
        // init Quill, lalu restore state collapsed (kalau caller tidak override).
        var wasCollapsed = card.classList.contains('is-collapsed');
        if (wasCollapsed) {
            card.classList.remove('is-collapsed');
            card.classList.add('is-open');
        }

        var quill = new Quill(editorEl, {
            theme: 'snow',
            modules: {
                toolbar: opts.wysiwygToolbar || [
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['bold', 'italic', 'underline'],
                    [{ align: [] }],
                    ['clean'],
                ],
            },
            placeholder: opts.wysiwygPlaceholder || 'Tulis konten di sini.',
        });
        editorEl.__quill = quill;
        var sync = function () { inputEl.value = quill.root.innerHTML; };
        quill.on('text-change', sync);
        sync();

        // Setelah Quill init sukses, restore state collapsed.
        // Pakai double-rAF supaya Quill sempat hitung layout dulu
        // sebelum body di-collapse lagi (tinggi 0).
        // Hanya restore kalau caller tidak set flag __lpForceOpen
        // (mis. AddBtn ingin card tetap terbuka setelah init).
        if (wasCollapsed && !card.__lpForceOpen) {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    card.classList.remove('is-open');
                    card.classList.add('is-collapsed');
                });
            });
        }
    }

    function initToggles(card) {
        card.querySelectorAll('.lp-rep-toggle input[type="checkbox"][data-role="publish"]').forEach(function (el) {
            var wrap = el.closest('.lp-rep-toggle');
            var icon = wrap ? wrap.querySelector('.lp-rep-toggle-icon i') : null;
            var text = wrap ? wrap.querySelector('.lp-rep-toggle-text') : null;
            var update = function () {
                if (!wrap) return;
                if (el.checked) {
                    wrap.classList.remove('is-off');
                    wrap.classList.add('is-on');
                    if (icon) icon.className = 'bi bi-check-lg';
                    if (text) text.textContent = 'Aktif';
                } else {
                    wrap.classList.remove('is-on');
                    wrap.classList.add('is-off');
                    if (icon) icon.className = 'bi bi-x-lg';
                    if (text) text.textContent = 'Non-aktif';
                }
            };
            el.addEventListener('change', update);
            update();
        });
    }

    function syncFilledInputs(card) {
        card.querySelectorAll('.input-group-outline input, .input-group-outline textarea, .input-group-outline select').forEach(function (el) {
            var wrap = el.closest('.input-group-outline');
            if (!wrap) return;
            if (el.type === 'checkbox' || el.type === 'radio' || el.type === 'file') return;
            if (el.value !== null && el.value !== '') wrap.classList.add('is-filled');
            else wrap.classList.remove('is-filled');
            el.addEventListener('input', function () { wrap.classList.add('is-filled'); });
        });
    }

    function initDeleteConfirm(scope) {
        scope.querySelectorAll('form[data-confirm]').forEach(function (form) {
            if (form.__lpConfirmBound) return;
            form.__lpConfirmBound = true;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var msg = form.getAttribute('data-confirm') || 'Yakin ingin menghapus data ini?';
                notify({
                    title: 'Hapus data?',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then(function (r) {
                    if (!r || !r.isConfirmed) return;
                    var fd = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': getCsrf(),
                            'Accept': 'application/json',
                        },
                        body: fd,
                    }).then(function (resp) {
                        var ct = resp.headers.get('content-type') || '';
                        if (ct.includes('application/json')) {
                            return resp.json().then(function (data) {
                                notify({
                                    icon: 'success',
                                    title: data.msg || 'Berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false,
                                }).then(function () { window.location.reload(); });
                            });
                        }
                        window.location.reload();
                    }).catch(function () {
                        notify({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.' });
                    });
                });
            });
        });
    }

    function init(opts) {
        var list = document.getElementById(opts.listId);
        if (!list) return;

        list.querySelectorAll('.' + opts.cardClass).forEach(function (card) {
            initQuillIn(card, opts);
            initToggles(card);
            syncFilledInputs(card);
            if (typeof opts.afterAppend === 'function') opts.afterAppend(card);
        });

        initDeleteConfirm(list);

        var addBtn = document.getElementById(opts.addBtnId);
        if (addBtn) {
            addBtn.addEventListener('click', function () {
                var tpl = document.getElementById(opts.templateId);
                var empty = document.getElementById(opts.emptyId);
                if (!tpl) return;
                if (empty) empty.remove();

                var idx = nextRowIndex(list, opts.namePrefix || 'rows');
                var node = tpl.content.firstElementChild.cloneNode(true);
                node.setAttribute('data-row-index', idx);
                node.querySelectorAll('[name^="' + (opts.namePrefix || 'rows') + '[__INDEX__]"], [id$="NEW"]').forEach(function (el) {
                    var attr = el.getAttribute('name');
                    if (attr) el.setAttribute('name', attr.replace((opts.namePrefix || 'rows') + '[__INDEX__]', (opts.namePrefix || 'rows') + '[' + idx + ']'));
                    var idAttr = el.getAttribute('id');
                    if (idAttr && idAttr.indexOf('NEW') !== -1) {
                        el.setAttribute('id', idAttr.replace('NEW', String(Date.now()) + '_' + idx));
                    }
                });

                var sortInput = node.querySelector('input[name*="[sort_order]"]');
                if (sortInput) sortInput.value = String(idx);

                list.appendChild(node);
                // Flag supaya initQuillIn TIDAK collapse lagi setelah init —
                // baris baru dari AddBtn harus tetap terbuka agar user bisa langsung
                // mengetik di input pertanyaan / WYSIWYG jawaban.
                node.__lpForceOpen = true;
                initQuillIn(node, opts);
                initToggles(node);
                syncFilledInputs(node);
                if (typeof opts.afterAppend === 'function') opts.afterAppend(node);
            });
        }

        list.addEventListener('click', function (e) {
            var rm = e.target.closest(opts.removeBtnSelector);
            if (!rm) return;
            var row = rm.closest('.' + opts.cardClass);
            if (row) row.remove();
        });

        var saveBtn = document.getElementById(opts.saveBtnId);
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                var rows = Array.prototype.slice.call(list.querySelectorAll('.' + opts.cardClass));
                if (!rows.length) {
                    notify({ icon: 'info', title: 'Tidak ada baris', text: 'Tambahkan minimal satu baris.' });
                    return;
                }

                var queue = [];
                rows.forEach(function (row) {
                    var payload = opts.gatherPayload(row);
                    if (!payload) return;
                    var id = row.getAttribute('data-id');
                    var url = id
                        ? opts.updateUrlTpl.replace('__ID__', id)
                        : opts.storeUrl;
                    queue.push({ row: row, fd: payload.fd, url: url, isNew: !id });
                });

                if (!queue.length) {
                    notify({ icon: 'warning', title: 'Belum ada data', text: 'Isi data pada minimal satu baris.' });
                    return;
                }

                saveBtn.dataset.busy = '1';
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                var i = 0;
                var failures = 0;
                var newCount = 0;
                var updCount = 0;

                function submitNext() {
                    if (i >= queue.length) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<span class="material-symbols-rounded" style="font-size:16px;">save</span> Simpan Semua';
                        saveBtn.dataset.busy = '0';
                        if (failures) {
                            notify({
                                icon: 'error',
                                title: 'Sebagian gagal disimpan',
                                text: failures + ' baris tidak tersimpan. Coba lagi.',
                            });
                        } else {
                            var summary = (newCount ? (newCount + ' ditambah') : '')
                                + (newCount && updCount ? ', ' : '')
                                + (updCount ? (updCount + ' diperbarui') : '');
                            notify({
                                icon: 'success',
                                title: 'Perubahan tersimpan',
                                text: summary || 'Data berhasil disimpan.',
                            });
                            setTimeout(function () { window.location.reload(); }, 900);
                        }
                        return;
                    }

                    var job = queue[i++];
                    // Untuk update (PUT/PATCH), tambahkan _method override karena FormData
                    // fetch() tidak bisa pakai method selain POST/GET saat mengirim multipart.
                    if (!job.isNew) {
                        job.fd.append('_method', 'PUT');
                    }
                    fetch(job.url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': getCsrf(),
                            'Accept': 'application/json',
                        },
                        body: job.fd,
                    }).then(function (resp) {
                        return resp.json().then(function (data) {
                            return { status: resp.status, data: data };
                        }).catch(function () {
                            return { status: resp.status, data: null };
                        });
                    }).then(function (out) {
                        if (!out || !out.status || out.status >= 400) {
                            failures++;
                            if (out && out.data && out.data.errors) {
                                var firstField = Object.keys(out.data.errors)[0];
                                var firstMsg = firstField ? out.data.errors[firstField][0] : null;
                                if (firstMsg) {
                                    notify({ icon: 'error', title: 'Validasi gagal', text: firstMsg });
                                } else {
                                    notify({ icon: 'error', title: 'Gagal menyimpan baris', text: 'Periksa data Anda.' });
                                }
                            } else if (out && out.data && out.data.msg) {
                                notify({ icon: 'error', title: 'Gagal menyimpan', text: out.data.msg });
                            } else {
                                notify({ icon: 'error', title: 'Gagal menyimpan baris', text: 'Respons tidak valid.' });
                            }
                        } else {
                            if (job.isNew) newCount++; else updCount++;
                        }
                    }).catch(function (err) {
                        failures++;
                        notify({ icon: 'error', title: 'Tidak terhubung', text: 'Periksa koneksi Anda.' });
                    }).then(function () {
                        submitNext();
                    });
                }

                submitNext();
            });
        }
    }

    function nextRowIndex(list, prefix) {
        var max = 0;
        list.querySelectorAll('[name^="' + prefix + '["]').forEach(function (el) {
            var m = (el.getAttribute('name') || '').match(new RegExp('^' + prefix + '\\[(\\d+)\\]'));
            if (m) { var v = parseInt(m[1], 10); if (!isNaN(v) && v > max) max = v; }
        });
        return max + 1;
    }

    function buildFormData(row, fields, opts) {
        var fd = new FormData();
        fields.forEach(function (f) {
            if (f.wysiwyg) {
                var inputEl = row.querySelector('[data-role="items-input"]');
                var html = inputEl ? inputEl.value : '';
                if (opts && opts.itemsAsJson) {
                    var arr = htmlListToItems(html);
                    fd.append(f.name, JSON.stringify(arr));
                } else {
                    fd.append(f.name, html);
                }
            } else if (f.type === 'checkbox') {
                var cb = row.querySelector('input[type="checkbox"][name*="[' + f.name + ']"]');
                fd.append(f.name, cb && cb.checked ? '1' : '0');
            } else {
                var inp = row.querySelector('input[name*="[' + f.name + ']"], textarea[name*="[' + f.name + ']"], select[name*="[' + f.name + ']"]');
                if (!inp) return;
                fd.append(f.name, inp.value);
            }
        });
        return fd;
    }

    return {
        init: init,
        htmlListToItems: htmlListToItems,
        buildFormData: buildFormData,
        nextRowIndex: nextRowIndex,
    };
})();
</script>