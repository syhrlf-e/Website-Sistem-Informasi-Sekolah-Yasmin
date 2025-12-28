@component('mail::message')
# Pendaftaran PPDB Berhasil! 🎉

Halo **{{ $registration->nama_lengkap }}**,

Terima kasih telah mendaftar di **SMA Mutiara Insan Nusantara**. Pendaftaran Anda telah kami terima dengan data sebagai
berikut:

@component('mail::panel')
**Nomor Registrasi:** {{ $registration->registration_number }}
**Token Akses:** {{ $registration->token }}

> Simpan data ini baik-baik untuk mengecek status pendaftaran Anda.
@endcomponent

## Ringkasan Data Pendaftaran

| Informasi | Data |
| :--- | :--- |
| Nama Lengkap | {{ $registration->nama_lengkap }} |
| Jurusan Pilihan | {{ $registration->jurusan_pilihan }} |
| Asal Sekolah | {{ $registration->asal_sekolah }} |
| No. HP | {{ $registration->no_hp }} |

@component('mail::button', ['url' => $url])
Cek Status Pendaftaran
@endcomponent

## Langkah Selanjutnya

1. Simpan **Nomor Registrasi** dan **Token** Anda
2. Pantau status pendaftaran melalui website kami
3. Tunggu informasi lebih lanjut dari panitia PPDB

Jika ada pertanyaan, silakan hubungi panitia PPDB kami.

Salam hangat,
**Panitia PPDB SMA Mutiara Insan Nusantara**
@endcomponent