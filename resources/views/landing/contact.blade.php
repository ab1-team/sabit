@extends('landing.layout')

@section('title', 'Kontak — ' . $setting->school_name)

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Kontak Kami</h2>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">{{ $setting->school_name }}</h6>

                        @if ($setting->address)
                            <p class="small mb-2">{{ $setting->address }}</p>
                        @endif
                        @if ($setting->phone)
                            <p class="small mb-1">Telepon: {{ $setting->phone }}</p>
                        @endif
                        @if ($setting->whatsapp)
                            <p class="small mb-1">WhatsApp: {{ $setting->whatsapp }}</p>
                        @endif
                        @if ($setting->email)
                            <p class="small mb-0">Email: {{ $setting->email }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Kirim Pesan</h6>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('landing.contact.store') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name') }}" required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email') }}" required maxlength="150">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subjek</label>
                                    <input type="text" name="subject" class="form-control"
                                           value="{{ old('subject') }}" maxlength="200">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Pesan <span class="text-danger">*</span></label>
                                    <textarea name="message" class="form-control" rows="5"
                                              required maxlength="5000">{{ old('message') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if ($setting->google_maps_url)
            <div class="ratio ratio-21x9 mt-4">
                <iframe src="{{ $setting->google_maps_url }}" loading="lazy"
                        style="border:0" allowfullscreen></iframe>
            </div>
        @endif
    </div>
</section>
@endsection
