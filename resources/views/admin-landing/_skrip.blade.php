<script>
(function () {
    function getCsrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function notify(opts) {
        // Fallback kalau SweetAlert2 gagal dimuat: pakai alert() standar.
        if (typeof Swal === 'undefined' || !Swal || !Swal.fire) {
            var text = (opts && opts.title ? opts.title + ': ' : '') + (opts && (opts.text || opts.html) || '');
            try { window.alert(text); } catch (e) {}
            return Promise.resolve();
        }
        return Swal.fire(opts);
    }

    function fillFromOld(el, oldValue) {
        if (oldValue === null || oldValue === undefined || oldValue === '') return;
        if (el.tagName === 'SELECT') {
            var exists = false;
            for (var i = 0; i < el.options.length; i++) {
                if (el.options[i].value === String(oldValue)) { exists = true; break; }
            }
            if (!exists) {
                var opt = document.createElement('option');
                opt.value = String(oldValue);
                opt.textContent = String(oldValue);
                opt.selected = true;
                el.appendChild(opt);
            } else {
                el.value = String(oldValue);
            }
        } else if (el.type === 'checkbox') {
            el.checked = !!oldValue;
        } else {
            el.value = oldValue;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

        // ============== Material outlined: auto is-filled ==============
        document.querySelectorAll('.input-group-outline input, .input-group-outline textarea, .input-group-outline select').forEach(function (el) {
            var wrap = el.closest('.input-group-outline');
            if (!wrap) return;
            if (el.type === 'checkbox' || el.type === 'radio' || el.type === 'file') return;
            var hasValue = el.value !== null && el.value !== '';
            if (hasValue) wrap.classList.add('is-filled');
            el.addEventListener('input', function () { wrap.classList.add('is-filled'); });
            el.addEventListener('change', function () { wrap.classList.add('is-filled'); });
        });

        // ============== Select2 init (semua select dengan class .lp-select) ==============
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('select.lp-select').each(function () {
                var $el = jQuery(this);
                if ($el.data('select2')) return;
                $el.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: $el.attr('placeholder') || $el.attr('data-placeholder') || '-- Pilih --',
                    allowClear: !$el.prop('required'),
                    minimumResultsForSearch: $el.find('option').length > 8 ? 0 : -1,
                }).on('change', function () {
                    var wrap = this.closest('.input-group-outline');
                    if (wrap) wrap.classList.add('is-filled');
                });
            });
        }

        // ============== Flatpickr init ==============
        if (window.flatpickr) {
            flatpickr('.lp-date', {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: true,
                allowInput: true,
            });
            flatpickr('.lp-date-only', {
                dateFormat: 'Y-m-d',
                allowInput: true,
            });
        }

        // ============== maskMoney untuk input .lp-money ==============
        if (window.jQuery && jQuery.fn.maskMoney) {
            jQuery('input.lp-money').each(function () {
                if (jQuery(this).data('maskMoney')) return;
                jQuery(this).maskMoney({
                    prefix: 'Rp ',
                    thousands: '.',
                    decimal: ',',
                    precision: 0,
                    allowZero: true,
                    allowNegative: false,
                });
            });
        }

        // ============== Preview box file upload (klik box -> lihat gambar) ==============
        document.querySelectorAll('.lp-preview-box').forEach(function (box) {
            var inputId = box.getAttribute('for');
            if (!inputId) {
                var idMatch = box.id ? box.id.replace('PreviewBox', 'Input') : null;
                if (idMatch) inputId = idMatch;
            }
            if (!inputId) return;
            var input = document.getElementById(inputId);
            if (!input) return;

            input.addEventListener('change', function () {
                if (!input.files || !input.files[0]) return;
                var file = input.files[0];
                if (!file.type || file.type.indexOf('image/') !== 0) {
                    box.querySelectorAll('img').forEach(function (img) { img.remove(); });
                    var placeholder = document.createElement('span');
                    placeholder.className = 'material-symbols-rounded lp-preview-empty';
                    placeholder.textContent = 'description';
                    box.insertBefore(placeholder, box.firstChild);
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    var existing = box.querySelector('img');
                    if (existing) {
                        existing.src = e.target.result;
                    } else {
                        box.querySelectorAll('.lp-preview-empty').forEach(function (el) { el.remove(); });
                        var newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = 'preview';
                        box.insertBefore(newImg, box.firstChild);
                    }
                };
                reader.readAsDataURL(file);
            });
        });

        // ============== AJAX form submit (semua form .lp-ajax) ==============
        document.querySelectorAll('form.lp-ajax').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var btn = form.querySelector('[type="submit"]');
                if (btn && btn.dataset.busy === '1') return;
                if (btn) {
                    btn.dataset.busy = '1';
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
                }

                var formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrf(),
                        'Accept': 'application/json',
                    },
                    body: formData,
                }).then(function (resp) {
                    var ct = resp.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        // fallback: redirect biasa
                        window.location.href = form.action;
                        return null;
                    }
                    return resp.json().then(function (data) { return { status: resp.status, data: data }; });
                }).then(function (out) {
                    if (!out) return;
                    if (out.status === 422 && out.data && out.data.errors) {
                        // Tampilkan error validasi sebagai toast ringkas (pojok kanan atas).
                        var errs = out.data.errors;
                        var firstField = Object.keys(errs)[0];
                        var firstMsg = firstField ? errs[firstField][0] : 'Data tidak valid';
                        notify({
                            icon: 'error',
                            title: 'Data belum lengkap',
                            text: firstMsg,
                            toast: true,
                            position: 'top-end',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                        return;
                    }
                    if (out.data && out.data.success) {
                        notify({
                            icon: 'success',
                            title: out.data.msg || 'Tersimpan',
                            toast: true,
                            position: 'top-end',
                            timer: 1800,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        }).then(function () {
                            if (out.data.redirect) {
                                window.location.href = out.data.redirect;
                            } else {
                                // tetap di halaman: refresh state tombol
                                if (btn) {
                                    btn.disabled = false;
                                    btn.innerHTML = btn.dataset.originalHtml || 'Simpan';
                                    btn.dataset.busy = '0';
                                }
                            }
                        });
                    } else {
                        notify({
                            icon: 'error',
                            title: 'Gagal',
                            text: (out.data && out.data.msg) || 'Terjadi kesalahan',
                            toast: true,
                            position: 'top-end',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                    }
                }).catch(function (err) {
                    notify({
                        icon: 'error',
                        title: 'Galat',
                        text: 'Cek koneksi / input Anda.',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                    console.error(err);
                }).finally(function () {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = btn.dataset.originalHtml || 'Simpan';
                        btn.dataset.busy = '0';
                    }
                });
            });
        });

        // ============== SweetAlert delete confirmation ==============
        document.querySelectorAll('form.lp-delete, form[onsubmit*="confirm"], form[data-confirm]').forEach(function (form) {
            if (form.hasAttribute('onsubmit')) form.removeAttribute('onsubmit');
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
                    if (r.isConfirmed) {
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
                    }
                });
            });
        });

        // ============== Tabs handler (untuk halaman Pengaturan Konten) ==============
        document.querySelectorAll('[data-lp-tab-group]').forEach(function (group) {
            var tabs = group.querySelectorAll('[data-lp-tab]');
            var panes = document.querySelectorAll('[data-lp-tab-pane]');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var target = tab.getAttribute('data-lp-tab');
                    tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
                    panes.forEach(function (p) {
                        p.classList.toggle('d-none', p.getAttribute('data-lp-tab-pane') !== target);
                    });
                });
            });
        });

        // ============== Repeater row (untuk hero_badges, stats, dll) ==============
        document.querySelectorAll('[data-add-row]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var wrapId = btn.getAttribute('data-add-row');
                var wrap = document.getElementById(wrapId);
                if (!wrap) return;
                var templateClass = btn.getAttribute('data-template');
                var source = wrap.querySelector('.' + templateClass);
                if (!source) return;
                var clone = source.cloneNode(true);
                var newIndex = wrap.querySelectorAll('.' + templateClass).length;
                clone.querySelectorAll('input, select, textarea').forEach(function (el) {
                    var name = el.getAttribute('name');
                    if (!name) return;
                    el.setAttribute('name', name.replace(/\[\d+\]/, '[' + newIndex + ']'));
                    if (el.type === 'checkbox') el.checked = false;
                    else el.value = '';
                });
                clone.querySelectorAll('select.lp-select').forEach(function (el) {
                    jQuery(el).val(null).trigger('change');
                });
                wrap.appendChild(clone);
                attachRemoveHandlers();
            });
        });

        function attachRemoveHandlers() {
            document.querySelectorAll('.btn-remove-row').forEach(function (b) {
                b.onclick = function () {
                    var row = b.closest('.hero-badge-row, .stats-row, .jenjang-row, .keunggulan-row');
                    if (row) row.remove();
                };
            });
        }
        attachRemoveHandlers();
    });
})();
</script>
