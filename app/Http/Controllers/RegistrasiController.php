<?php

namespace App\Http\Controllers;

use App\Imports\RegisImport;
use App\Models\AhliWaris;
use App\Models\Konfigurasi;
use App\Models\Makam;
use App\Models\Registrasi;
use App\Models\Retribusi;
use Auth;
use Crypt;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RegistrasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function formulir(Request $request)
    {
        $data = Registrasi::with('ahliwaris', 'makam', 'retribusi')->where('id', $request->id)->first();

        return view('pages.registrasi.formulir', compact('data'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tahun = $request->tahun;
        // Auth::user()->roles->first()->display_name;
        $tpu = Auth::user()->roles->first()->display_name;

        if ($tahun != null) {
            /* case 1. email or password is wrong */
            $data = Registrasi::with('ahliwaris', 'makam', 'retribusi')
            ->whereHas('makam', function ($q) use ($tahun) {
                // Query the name field in status table
                $q->whereYear('tanggal_meninggal', '=', $tahun); // '=' is optional
            })
            ->get();
        } elseif ($tpu != 'Admin') {
            $data = Registrasi::with('ahliwaris', 'makam', 'retribusi')
            ->whereHas('makam', function ($q) use ($tpu) {
                // Query the name field in status table
                $q->where('nama_tpu', '=', $tpu); // '=' is optional
            })
            ->get();
        } else {
            /* case 2. email is not confirmed */
            $data = Registrasi::with('ahliwaris', 'makam', 'retribusi')->get();
        }

        return view('pages.registrasi.index', [
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = json_decode('{"verifikasi": "FALSE"}');
        $konfig = Konfigurasi::get();
        $uraian = Konfigurasi::where('id', 1)->first();

        return view('pages.registrasi.form', compact('data', 'konfig', 'uraian'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // inisiasi
        $regId = $request->regId;
        $awID = $request->awID;
        $makamID = $request->makamID;
        $retriID = $request->retriID;

        $nominal = 40000;
        $kodeReg = rand(100000, 999999);

        $string = ['Rp', ',00', '.'];

        $rules = [
            // 'nama' => 'required',
            // 'tempat_lahir1' => 'required',
            // 'tanggal_lahir1' => 'required',
            // 'jenis_kelamin1' => 'required',
            // 'nik1' => 'required',
            // 'agama1' => 'required',
            // 'pekerjaan1' => 'required',
            // 'alamat1' => 'required',
            // 'kode_registrasi' => 'required',
            // 'ahliwaris_id' => 'required',
            // 'hubungan' => 'required',
            // 'nama_meninggal' => 'required',
            // 'tempat_lahir2' => 'required',
            // 'tanggal_lahir2' => 'required',
            // 'jenis_kelamin2' => 'required',
            // 'nik2' => 'required',
            // 'agama2' => 'required',
            // 'pekerjaan2' => 'required',
            // 'alamat2' => 'required',
            // 'tanggal_meninggal' => 'required',
            // 'tanggal_dimakamkan' => 'required',
            // 'luas_lahan' => 'required',
            // 'nama_tpu' => 'required',
            // 'blok_tpu' => 'required',
            // 'nomor_tpu' => 'required',
            // 'nama_ditumpang' => 'required',
            // 'registrasi_id' => 'required',
            // 'retribusi.*.korek' => 'required',
            // 'retribusi.*.uraian' => 'required',
            // 'retribusi.*.nominal' => 'required',
        ];
        $customMessages = [
            'required' => ':attribute tidak boleh kosong ',
            'unique' => ':attribute sudah digunakan',

        ];
        $attributeNames = [
            'nama' => 'Nama Ahli Waris/Penanggung Jawab',
            'tempat_lahir1' => 'Tempat Lahir Ahli Waris/Penanggung Jawab',
            'tanggal_lahir1' => 'Tanggal Lahir Ahli Waris/Penanggung Jawab',
            'jenis_kelamin1' => 'Jenis Kelamin Ahli Waris/Penanggung Jawab',
            'nik1' => 'NIK Ahli Waris/Penanggung Jawab',
            'agama1' => 'Agama Ahli Waris/Penanggung Jawab',
            'pekerjaan1' => 'Pekerjaan Ahli Waris/Penanggung Jawab',
            'alamat1' => 'Alamat Ahli Waris/Penanggung Jawab',
            'kode_registrasi' => 'Kode Registrasi',
            'ahliwaris_id' => 'Ahli Waris',
            'hubungan' => 'Hubungan dengan Ahli Waris',
            'nama_meninggal' => 'Nama Orang yang Meninggal',
            'tempat_lahir2' => 'Tempat Lahir Orang yang Meninggal',
            'tanggal_lahir2' => 'Tanggal Lahir Orang yang Meninggal',
            'jenis_kelamin2' => 'Jenis Kelamin Orang yang Meninggal',
            'nik2' => 'NIK Orang yang Meninggal',
            'agama2' => 'Agama Orang yang Meninggal',
            'pekerjaan2' => 'Pekerjaan Orang yang Meninggal',
            'alamat2' => 'Alamat Orang yang Meninggal',
            'tanggal_meninggal' => 'Tanggal Meninggal',
            'tanggal_dimakamkan' => 'Tanggal Dimakamkan',
            'luas_lahan' => 'Luas Lahan',
            'nama_tpu' => 'Nama TPU',
            'blok_tpu' => 'Blok TPU',
            'nomor_tpu' => 'No TPU',
            'nama_ditumpang' => 'Nama Ditumpang',
            'registrasi_id' => 'Registrasi ID',
            'retribusi.*.korek' => 'Kode Rekening',
            'retribusi.*.uraian' => 'Uraian',
            'retribusi.*.nominal' => 'Nominal',
        ];

        $valid = $this->validate($request, $rules, $customMessages, $attributeNames);
        $ahliwaris = AhliWaris::updateOrCreate(
            [
                'id' => $awID,
            ],
            [
                'nama' => $request->nama,
                'tempat_lahir1' => $request->tempat_lahir1,
                'tanggal_lahir1' => $request->tanggal_lahir1,
                'jenis_kelamin1' => $request->jenis_kelamin1,
                // 'nik1' => Crypt::encryptString($request->nik1),
                'nik1' => $request->nik1,
                'agama1' => $request->agama1,
                'pekerjaan1' => $request->pekerjaan1,
                'alamat1' => $request->alamat1,
            ]);
        $registrasi = Registrasi::updateOrCreate(
            [
                'id' => $regId,
            ],
            [
                'kode_registrasi' => $kodeReg,
                'ahliwaris_id' => $ahliwaris->id,
                'hubungan' => $request->hubungan,
                'nama_meninggal' => $request->nama_meninggal,
                'tempat_lahir2' => $request->tempat_lahir2,
                'tanggal_lahir2' => $request->tanggal_lahir2,
                'jenis_kelamin2' => $request->jenis_kelamin2,
                // 'nik2' => Crypt::encryptString($request->nik2),
                'nik2' => $request->nik2,
                'agama2' => $request->agama2,
                'pekerjaan2' => $request->pekerjaan2,
                'alamat2' => $request->alamat2,
                'verifikasi' => $request->verifikasi,
                'verif_oleh' => $request->verif_oleh,
            ]);
        $makam = Makam::updateOrCreate(
            [
                'id' => $makamID,
            ],
            [
                'registrasi_id' => $registrasi->id,
                'tanggal_meninggal' => $request->tanggal_meninggal,
                'tanggal_dimakamkan' => $request->tanggal_dimakamkan,
                'luas_lahan' => $request->luas_lahan,
                'luas_lahan1' => $request->luas_lahan1,
                'luas_lahan2' => $request->luas_lahan2,
                'nama_tpu' => $request->nama_tpu,
                'blok_tpu' => $request->blok_tpu,
                'nomor_tpu' => $request->nomor_tpu,
                'nama_ditumpang' => $request->nama_ditumpang,
                
            ]
        );
        // $retribusi = Retribusi::updateOrCreate(
        //     [
        //         'id' => $retriID,
        //     ],
        //     [
        //         'registrasi_id' => $registrasi->id,
        //         'kode_rekening' => $request->kode_rekenning,
        //         'uraian' => $request->uraian,
        //         'nominal' => $request->nominal,
        //     ]
        // );
        if ($request->retribusi != null) {
            # code...
            $i = 1;
            foreach ($request->retribusi as $key => $value) {
                Retribusi::updateOrCreate(
                    [
                        'id' => $value['id'],
    
                    ], [
                        'registrasi_id' => $registrasi->id,
                        'korek' => $value['korek'],
                        'uraian' => $value['uraian'],
                        'nominal' => str_replace($string, '', $value['nominal']),
                    ]);
            }
        }
        return redirect()->back()->with('message', 'Data Berhasil Disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Registrasi  $registrasi
     * @return \Illuminate\Http\Response
     */
    public function show(Registrasi $registrasi)
    {
        //
    }

    public function verif(Request $request)
    {
        $rules = [
            'verif_oleh' => 'required',
            'verifikasi' => 'required',

        ];
        $customMessages = [
            'required' => ':attribute tidak boleh kosong ',

        ];
        $attributeNames = [
            'verif_oleh' => 'Nama Verifikator Wajib Diisi!',
            'verifikasi' => 'Verifikasi Wajib Dicentang',

        ];

        $valid = $this->validate($request, $rules, $customMessages, $attributeNames);
        $registrasi = Registrasi::updateOrCreate(
            [
                'id' => $request->regID,
            ],
            [
                'verifikasi' => $request->verifikasi,
                'verif_oleh' => $request->verif_oleh,
            ]);

        return redirect()->back()->with('message', 'Data Berhasil Diverifikasi');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Registrasi  $registrasi
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $data = Registrasi::with('ahliwaris', 'makam', 'retribusi')->where('id', $request->id)->first();
        $konfig = Konfigurasi::get();

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('pages.registrasi.form', compact('data', 'konfig'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Registrasi  $registrasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = Registrasi::with('ahliwaris', 'makam', 'retribusi')->where('id', $request->id)->first();

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Registrasi  $registrasi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = Registrasi::where('id', $request->id)->delete();
        // return redirect()->back()->with('message', 'Data Berhasil Dihapus');
        return response()->json($data, 200);
    }

    public function destroyRetri(Request $request)
    {
        $data = Retribusi::where('id', $request->id)->delete();
        // return redirect()->back()->with('message', 'Data Berhasil Dihapus');
        return response()->json($data, 200);
    }

    public function fileImport(Request $request)
    {
        Excel::import(new RegisImport, $request->file('file')->store('temp'));

        return back();
    }
}
