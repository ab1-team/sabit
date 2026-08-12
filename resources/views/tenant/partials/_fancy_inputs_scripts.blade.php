{{-- Scripts include: jQuery, Select2, flatpickr, maskMoney + auto-init helpers --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
<script>
    (function () {
        function initSelect2(scope) {
            $(scope).find('select').each(function () {
                var $el = $(this);
                if ($el.data('select2')) { $el.select2('destroy'); }
                var placeholder = $el.data('placeholder') || $el.find('option[value=""]').first().text() || 'Pilih';
                var parentModal = $el.closest('[id$="-modal"], [id$="_modal"]');
                $el.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: placeholder,
                    allowClear: false,
                    minimumResultsForSearch: $el.find('option').length <= 5 ? Infinity : 0,
                    dropdownParent: parentModal.length ? parentModal : $(document.body)
                });
            });
        }
        function initNominal(scope) {
            $(scope).find('input.nominal').each(function () {
                var $el = $(this);
                if ($el.data('maskMoney')) return;
                $el.maskMoney({
                    prefix: '',
                    thousands: ',',
                    decimal: '.',
                    precision: 0,
                    allowZero: true,
                    allowNegative: false
                });
                var v = $el.val();
                if (v !== '' && v !== null && typeof v !== 'undefined') {
                    $el.maskMoney('mask', Number(String(v).replace(/[^0-9.]/g, '')));
                }
            });
        }
        function initDatepicker(scope) {
            $(scope).find('input.datepicker').each(function () {
                if (this._flatpickr) return;
                this._flatpickr = flatpickr(this, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd F Y',
                    altInputClass: 'invoice-input',
                    allowInput: true,
                    disableMobile: true
                });
            });
        }
        window.initFancyInputs = function (selector) {
            initSelect2(selector);
            initNominal(selector);
            initDatepicker(selector);
        };
    })();
</script>
