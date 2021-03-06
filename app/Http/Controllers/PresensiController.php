<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Symfony\Component\VarDumper\Cloner\Data;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('presensi.masuk');
    }

    public function keluar()
    {
        return view('presensi.keluar');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $timezone = 'Asia/Jakarta';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $tanggal = $date->format('Y-m-d');
        $localtime = $date->format('H:i:s');

        $presensi = Presensi::where([
            ['user_id', '=', auth()->user()->id],
            ['tgl', '=', $tanggal],
        ])->first();

        if ($presensi) {
            dd("ada");
        } else {
            Presensi::create([
                'user_id' => auth()->user()->id,
                'tgl' => $tanggal,
                'jammasuk' => $localtime,
            ]);
        }

        return redirect('presensi-masuk');
    }


    public function presensipulang()
    {
        $timezone = 'Asia/Jakarta';
        $date = new DateTime('now', new DateTimeZone($timezone));
        $tanggal = $date->format('Y-m-d');
        $localtime = $date->format('H:i:s');

        $presensi = Presensi::where([
            ['user_id', '=', auth()->user()->id],
            ['tgl', '=', $tanggal],
        ])->first();

        $dt = [
            'jamkeluar' => $localtime,
            'jamkerja' => date('H:i:s', strtotime($localtime) - strtotime($presensi->jammasuk))
        ];

        if ($presensi->jamkeluar == "") {
            $presensi->update($dt);
            return redirect('presensi-keluar');
        } else {
            $presensi->update($dt);
        }

        return redirect('presensi-masuk');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function halamanrekap()
    {
        return view('presensi.halaman-rekap');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function tampildatakeseluruhan($tglawal, $tglakhir)
    {
        $presensi = Presensi::whereBetween('tgl', [$tglawal, $tglakhir])->orderBy('tgl', 'asc')->get();
        return view('presensi.rekap-karyawan', compact('presensi'));
    }
    public function tampildataperkaryawan($tglawal, $tglakhir, $id)
    {
        $presensi = Presensi::where('user_id', $id)->whereBetween('tgl', [$tglawal, $tglakhir])->orderBy('tgl', 'asc')->get();
        return view('presensi.rekap-karyawan', compact('presensi'));
    }
}
