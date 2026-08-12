{{-- Head include: CSS Select2 & flatpickr --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
    .select2-container { width: 100% !important; }
    .select2-container--bootstrap-5 .select2-selection { min-height: 44px; border-color: #cbd5e1; border-radius: .75rem; padding: .375rem .875rem; font-size: .875rem; }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding: .25rem 1.5rem .25rem 0; color: #1e293b; line-height: 1.75rem; }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { top: 9px; right: 10px; }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
    .select2-container--bootstrap-5 .select2-dropdown { border-color: #cbd5e1; border-radius: .75rem; overflow: hidden; }
    .modal-scroll .select2-container--open { z-index: 70; }
    .modal-scroll .select2-container--bootstrap-5 .select2-dropdown { z-index: 70; }
    .modal-scroll .select2-container--bootstrap-5 .select2-search__field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, .15); }
    .flatpickr-input { cursor: pointer; }
    .flatpickr-calendar { z-index: 60; }
    .modal-scroll .flatpickr-calendar { box-shadow: 0 10px 30px rgba(15, 23, 42, .16); }
    .modal-scroll .flatpickr-day.selected, .modal-scroll .flatpickr-day.startRange, .modal-scroll .flatpickr-day.endRange { background: #4f46e5; border-color: #4f46e5; }
    .modal-scroll .flatpickr-day.today { border-color: #6366f1; }
    .modal-scroll .flatpickr-months .flatpickr-month, .modal-scroll .flatpickr-current-month .flatpickr-monthDropdown-months { color: #1e293b; }
    .modal-scroll .nominal { letter-spacing: .01em; }
    .modal-scroll .nominal:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
</style>
