<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create ppdb_registrations table
 * 
 * This table stores all PPDB applicant data based on Google Form structure:
 * - Identitas Diri (16 fields)
 * - Tempat Tinggal (5 fields)  
 * - Jurusan (1 field)
 * - Kesehatan (6 fields)
 * - Pendidikan (4 fields)
 * - Orang Tua (13 fields)
 * - Wali (7 fields - nullable)
 * - System fields
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('ppdb_registrations', function (Blueprint $table) {
            $table->id();

            // ===== SYSTEM FIELDS =====
            $table->string('registration_number')->unique();  // PPDB-2025-0001
            $table->string('token', 10);                      // 6-char verification token
            $table->enum('status', [
                'pending',      // Baru submit
                'verified',     // Data terverifikasi
                'selection',    // Tahap seleksi
                'accepted',     // Diterima
                'rejected'      // Ditolak
            ])->default('pending');
            $table->foreignId('wave_id')->constrained('ppdb_waves')->onDelete('cascade');

            // ===== SECTION 1: IDENTITAS DIRI (16 fields) =====
            $table->string('nama_lengkap');
            $table->string('nik', 16);
            $table->string('nisn', 10)->nullable();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->string('kewarganegaraan')->default('Indonesia');
            $table->integer('anak_ke');
            $table->integer('jumlah_saudara');
            $table->string('no_hp');
            $table->string('email')->nullable();
            $table->string('hobi')->nullable();
            $table->string('cita_cita')->nullable();
            $table->string('prestasi_akademik')->nullable();
            $table->string('prestasi_non_akademik')->nullable();

            // ===== SECTION 2: TEMPAT TINGGAL (5 fields) =====
            $table->text('alamat_lengkap');
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kota_kabupaten');
            $table->string('provinsi');
            $table->string('kode_pos', 10)->nullable();

            // ===== SECTION 3: JURUSAN (1 field) =====
            $table->enum('jurusan_pilihan', ['IPA', 'IPS']);

            // ===== SECTION 4: KESEHATAN (6 fields) =====
            $table->string('golongan_darah', 5)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();  // dalam cm
            $table->decimal('berat_badan', 5, 2)->nullable();   // dalam kg
            $table->text('riwayat_penyakit')->nullable();
            $table->boolean('alergi')->default(false);
            $table->text('keterangan_alergi')->nullable();

            // ===== SECTION 5: PENDIDIKAN ASAL (4 fields) =====
            $table->string('asal_sekolah');
            $table->string('npsn_sekolah', 20)->nullable();
            $table->text('alamat_sekolah')->nullable();
            $table->year('tahun_lulus');

            // ===== SECTION 6: ORANG TUA/WALI - AYAH (7 fields) =====
            $table->string('nama_ayah');
            $table->string('nik_ayah', 16)->nullable();
            $table->string('pekerjaan_ayah');
            $table->string('pendidikan_ayah');
            $table->string('penghasilan_ayah')->nullable();
            $table->string('no_hp_ayah')->nullable();
            $table->boolean('ayah_masih_hidup')->default(true);

            // ===== SECTION 7: ORANG TUA/WALI - IBU (6 fields) =====
            $table->string('nama_ibu');
            $table->string('nik_ibu', 16)->nullable();
            $table->string('pekerjaan_ibu');
            $table->string('pendidikan_ibu');
            $table->string('penghasilan_ibu')->nullable();
            $table->string('no_hp_ibu')->nullable();
            $table->boolean('ibu_masih_hidup')->default(true);

            // ===== SECTION 8: WALI (7 fields - all nullable for orphans/others) =====
            $table->string('nama_wali')->nullable();
            $table->string('nik_wali', 16)->nullable();
            $table->string('hubungan_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('pendidikan_wali')->nullable();
            $table->string('penghasilan_wali')->nullable();
            $table->string('no_hp_wali')->nullable();

            // ===== ADDITIONAL FIELDS =====
            $table->text('catatan_admin')->nullable();        // Internal notes
            $table->string('foto_path')->nullable();          // Pas foto
            $table->string('dokumen_path')->nullable();       // ZIP of required docs
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes for common queries
            $table->index('status');
            $table->index('wave_id');
            $table->index(['status', 'wave_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_registrations');
    }
};
