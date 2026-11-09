<?php

namespace Database\Seeders;

use App\Enums\FieldType;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FormBuilderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Base User (Google OAuth representation)
        $user = User::updateOrCreate(
            ['email' => 'admin@itbad.ac.id'],
            [
                'name' => 'Administrator ITBAD',
                'google_id' => '109876543210987654321',
                'avatar_url' => 'https://lh3.googleusercontent.com/a/default-avatar',
                'password' => null,
            ]
        );

        // 2. Seed Form 1: Workshop IT & AI 2026
        $workshopForm = Form::updateOrCreate(
            ['slug' => 'workshop-it-ai-2026'],
            [
                'user_id' => $user->id,
                'title' => 'Pendaftaran Workshop IT & AI 2026',
                'description' => 'Formulir pendaftaran resmi civitas akademika untuk Workshop Platform Form Builder & Automasi.',
                'target_spreadsheet_id' => '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms',
                'target_sheet_name' => 'PesertaWorkshop',
                'webhook_url' => 'https://api.internal.itbad.ac.id/webhooks/workshop',
                'webhook_secret' => 'whsec_d47f9e1c2b5a4a9c8f0e2d3b4a5c6d7e',
                'is_active' => true,
            ]
        );

        // Clear existing fields if re-seeding
        $workshopForm->fields()->delete();

        $fieldsForm1 = [
            [
                'label' => 'Nama Lengkap',
                'field_type' => FieldType::TEXT,
                'order_index' => 1,
                'is_required' => true,
                'placeholder' => 'Masukkan nama lengkap beserta gelar',
                'config' => ['max_length' => 150],
            ],
            [
                'label' => 'Email Institusi',
                'field_type' => FieldType::EMAIL,
                'order_index' => 2,
                'is_required' => true,
                'placeholder' => 'nama@itbad.ac.id',
                'config' => ['domain_restriction' => 'itbad.ac.id'],
            ],
            [
                'label' => 'Nomor WhatsApp',
                'field_type' => FieldType::PHONE,
                'order_index' => 3,
                'is_required' => true,
                'placeholder' => '081234567890',
                'config' => ['country_code' => '+62'],
            ],
            [
                'label' => 'Dosen Pembimbing / Afiliasi',
                'field_type' => FieldType::REMOTE_DATA_DROPDOWN,
                'order_index' => 4,
                'is_required' => false,
                'placeholder' => 'Ketik nama dosen atau program studi...',
                'config' => [
                    'spreadsheet_id' => '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms',
                    'sheet_name' => 'DataDosen',
                    'range' => 'A2:C',
                    'column_value' => 'A',
                    'column_label' => 'B',
                    'column_subtext' => 'C',
                ],
            ],
            [
                'label' => 'Kategori Peserta',
                'field_type' => FieldType::SELECT,
                'order_index' => 5,
                'is_required' => true,
                'placeholder' => '-- Pilih Status / Kategori --',
                'config' => [
                    'options' => ['Dosen', 'Mahasiswa', 'Tenaga Kependidikan', 'Umum'],
                ],
            ],
            [
                'label' => 'Sesi Workshop',
                'field_type' => FieldType::RADIO,
                'order_index' => 6,
                'is_required' => true,
                'placeholder' => null,
                'config' => [
                    'options' => ['Sesi 1: 09:00 - 12:00 WIB', 'Sesi 2: 13:30 - 16:30 WIB'],
                ],
            ],
            [
                'label' => 'Topik yang Diminati',
                'field_type' => FieldType::CHECKBOX,
                'order_index' => 7,
                'is_required' => false,
                'placeholder' => null,
                'config' => [
                    'options' => [
                        'Fullstack Modern Architecture',
                        'Google OAuth & Identity Flow',
                        'Spreadsheet API & Webhook Dispatching',
                    ],
                ],
            ],
            [
                'label' => 'Ekspektasi & Catatan Tambahan',
                'field_type' => FieldType::TEXTAREA,
                'order_index' => 8,
                'is_required' => false,
                'placeholder' => 'Tuliskan ekspektasi atau hal yang ingin Anda pelajari...',
                'config' => ['rows' => 4],
            ],
        ];

        foreach ($fieldsForm1 as $fieldData) {
            $workshopForm->fields()->create($fieldData);
        }

        // Submissions for Form 1
        $workshopForm->submissions()->delete();

        $workshopForm->submissions()->createMany([
            [
                'payload' => [
                    'nama_lengkap' => 'Dr. Hendra Gunawan, S.T., M.T.',
                    'email_institusi' => 'hendra.gunawan@itbad.ac.id',
                    'nomor_whatsapp' => '081298765432',
                    'kategori_peserta' => 'Dosen',
                    'sesi_workshop' => 'Sesi 1: 09:00 - 12:00 WIB',
                    'topik_yang_diminati' => [
                        'Google OAuth & Identity Flow',
                        'Spreadsheet API & Webhook Dispatching',
                    ],
                    'ekspektasi_catatan_tambahan' => 'Ingin mengintegrasikan form builder mandiri dengan sistem absensi kampus.',
                ],
                'sheet_synced' => true,
                'webhook_delivered' => true,
            ],
            [
                'payload' => [
                    'nama_lengkap' => 'Annisa Larasati',
                    'email_institusi' => 'annisa.laras@itbad.ac.id',
                    'nomor_whatsapp' => '085712345678',
                    'kategori_peserta' => 'Mahasiswa',
                    'sesi_workshop' => 'Sesi 2: 13:30 - 16:30 WIB',
                    'topik_yang_diminati' => [
                        'Fullstack Modern Architecture',
                    ],
                    'ekspektasi_catatan_tambahan' => 'Mempelajari cara membangun backend form yang modular dan scalable.',
                ],
                'sheet_synced' => false,
                'webhook_delivered' => false,
            ],
        ]);

        // 3. Seed Form 2: Evaluasi Kepuasan Layanan Sistem
        $evalForm = Form::updateOrCreate(
            ['slug' => 'evaluasi-layanan-sistem'],
            [
                'user_id' => $user->id,
                'title' => 'Survei Evaluasi Layanan Sistem Informasi',
                'description' => 'Evaluasi berkala kepuasan civitas terhadap infrastruktur IT dan keandalan sistem formulir digital.',
                'target_spreadsheet_id' => '1X9kLmN2oPqRsTuVwXyZ1234567890abcdefghijklm',
                'target_sheet_name' => 'EvaluasiSistem',
                'webhook_url' => null,
                'webhook_secret' => null,
                'is_active' => true,
            ]
        );

        $evalForm->fields()->delete();

        $fieldsForm2 = [
            [
                'label' => 'Fakultas / Program Studi',
                'field_type' => FieldType::TEXT,
                'order_index' => 1,
                'is_required' => true,
                'placeholder' => 'Contoh: Fakultas Teknologi Informasi',
                'config' => [],
            ],
            [
                'label' => 'Skor Kepuasan Kecepatan Respon',
                'field_type' => FieldType::RATING,
                'order_index' => 2,
                'is_required' => true,
                'placeholder' => null,
                'config' => ['scale' => 5, 'min_label' => 'Sangat Lambat', 'max_label' => 'Sangat Cepat'],
            ],
            [
                'label' => 'Saran Perbaikan Fitur',
                'field_type' => FieldType::TEXTAREA,
                'order_index' => 3,
                'is_required' => false,
                'placeholder' => 'Masukkan saran untuk pengembangan fitur selanjutnya...',
                'config' => ['rows' => 3],
            ],
        ];

        foreach ($fieldsForm2 as $fieldData) {
            $evalForm->fields()->create($fieldData);
        }

        // Submissions for Form 2
        $evalForm->submissions()->delete();

        $evalForm->submissions()->create([
            'payload' => [
                'fakultas_program_studi' => 'Fakultas Ilmu Komputer',
                'skor_kepuasan_kecepatan_respon' => 5,
                'saran_perbaikan_fitur' => 'Tampilan form sudah sangat bersih, tambahkan opsi ekspor PDF langsung dari dashboard.',
            ],
            'sheet_synced' => true,
            'webhook_delivered' => false,
        ]);
    }
}

