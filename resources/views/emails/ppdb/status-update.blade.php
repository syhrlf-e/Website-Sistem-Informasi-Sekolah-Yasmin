@component('mail::message')
# Update Status Pendaftaran PPDB

Halo **{{ $registration->nama_lengkap }}**,

Kami ingin menginformasikan bahwa status pendaftaran PPDB Anda telah diperbarui:

@component('mail::panel')
**Nomor Registrasi:** {{ $registration->registration_number }}

@if($registration->status === 'accepted')
    ## ✅ SELAMAT! Anda Diterima 🎉

    Dengan bangga kami sampaikan bahwa Anda telah **DITERIMA** sebagai calon siswa SMA Mutiara Insan Nusantara.
@elseif($registration->status === 'rejected')
    ## Status: Tidak Diterima

    Dengan hormat kami sampaikan bahwa Anda belum dapat diterima pada penerimaan tahun ini. Jangan menyerah, tetap semangat!
@elseif($registration->status === 'verified')
    ## Status: Terverifikasi ✓

    Data pendaftaran Anda telah diverifikasi oleh panitia. Silakan tunggu proses seleksi selanjutnya.
@elseif($registration->status === 'selection')
    ## Status: Dalam Proses Seleksi

    Pendaftaran Anda sedang dalam proses seleksi. Pantau terus status Anda.
@else
    ## Status: {{ ucfirst($registration->status) }}
@endif

**Status Sebelumnya:** {{ ucfirst($previousStatus) }}
**Status Sekarang:** {{ $registration->status_label }}
@endcomponent

@if($registration->catatan_admin)
    ## Catatan dari Panitia
    {{ $registration->catatan_admin }}
@endif

@component('mail::button', ['url' => $url])
Lihat Detail Status
@endcomponent

Jika ada pertanyaan, silakan hubungi panitia PPDB kami.

Salam hangat,
**Panitia PPDB SMA Mutiara Insan Nusantara**
@endcomponent