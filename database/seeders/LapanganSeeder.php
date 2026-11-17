<?php

namespace Database\Seeders;

use App\Models\Lapangan;
use Illuminate\Database\Seeder;

class LapanganSeeder extends Seeder
{
    public function run(): void
    {
        $lapangan = [
            [
                'nama_lapangan' => 'Lapangan Premium',
                'jenis_lapangan' => 'Indoor',
                'fasilitas' => 'Vinyl flooring',
                'harga_per_jam' => 50000,
                'status_lapangan' => 'tersedia',
                'nama_lama' => 'Lapangan Badminton 1',
            ],
            [
                'nama_lapangan' => 'Lapangan Standard',
                'jenis_lapangan' => 'Indoor',
                'fasilitas' => 'Sport flooring',
                'harga_per_jam' => 40000,
                'status_lapangan' => 'tersedia',
                'nama_lama' => 'Lapangan Badminton 2',
            ],
            [
                'nama_lapangan' => 'Lapangan Family',
                'jenis_lapangan' => 'Indoor',
                'fasilitas' => 'Area nyaman',
                'harga_per_jam' => 45000,
                'status_lapangan' => 'tersedia',
                'nama_lama' => 'Lapangan Badminton 3',
            ],
        ];

        foreach ($lapangan as $data) {
            $namaLama = $data['nama_lama'];
            unset($data['nama_lama']);

            $record = Lapangan::where('nama_lapangan', $namaLama)
                ->first()
                ?? Lapangan::where('nama_lapangan', $data['nama_lapangan'])->first();

            if ($record) {
                $record->update($data);

                continue;
            }

            Lapangan::create($data);
        }
    }
}
