<?php
 
namespace App\Exports;
 
use App\User;
use App\PemeriksaanGigi;
use App\RekamMedis;
use App\Identitas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;


use Carbon\Carbon;


// use Maatwebsite\Excel\Concerns\FromQuery;
// use Maatwebsite\Excel\Concerns\WithMapping;




class UsersExport implements WithStyles,WithProperties,WithEvents
{

    private $count;
    private $tahun;
    private $bulan;
    private $lastCount;
    private $jumlah;
    private $data;

    public function __construct($count,$tahun,$bulan,$jumlah) {
        $this->count = $count+3;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->jumlah = $jumlah;

        // $this->data = PemeriksaanGigi::count();
        $this->data = $this->laporanData($tahun,$bulan);


        
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 15
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '000'],
                    'size' => 1
                ]
            ]
            ];
        $jumlahStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '000'],
                    'size' => 1
                ]
                ],'font' => [
                    'bold' => true,
                    'size' => 15
                ],
                ];

        $tertandaStyle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
            ];
        
            $namaStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['argb' => '000'],
                        'size' => 1
                    ]
                ]
                ];
        
        return [
            // Handle by a closure.
            AfterSheet::class => function(AfterSheet $event) use ($styleArray,$jumlahStyle,$tertandaStyle,$namaStyle) {

                $event->sheet->mergeCells('A1:T1');
                $event->sheet->getStyle('A1')->applyFromArray($styleArray);
                $event->sheet->setCellValue('A1','LAPORAN KASUS PENYAKIT PENYAKIT GIGI DAN MULUT');


                // $event->sheet->mergeCells('A2:B2');
                // $event->sheet->setCellValue('A2','PUSKESMAS');

                // $event->sheet->mergeCells('C2:D2');
                // $event->sheet->setCellValue('C2','NAMA');


                $event->sheet->mergeCells('E2:F2');
                $event->sheet->setCellValue('E2','BULAN');

                

                $event->sheet->mergeCells('G2:H2');
                $event->sheet->setCellValue('G2',': '.$this->bulan);


                // $event->sheet->mergeCells('C2:d2');
                $event->sheet->setCellValue('K2','TH');

                $event->sheet->setCellValue('L2',': '.$this->tahun);

                $event->sheet->mergeCells('A3:A5');
                $event->sheet->setCellValue('A3','No');


                $event->sheet->mergeCells('B3:B5');
                $event->sheet->setCellValue('B3','Nama Penyakit');


                $event->sheet->mergeCells('C3:C5');
                $event->sheet->setCellValue('C3','ICD X');



                $event->sheet->mergeCells('D3:N3');
                $event->sheet->setCellValue('D3','KASUS BARU *)');

                $event->sheet->mergeCells('D4:E4');
                $event->sheet->setCellValue('D4','< 7 th');


                $event->sheet->mergeCells('F4:G4');
                $event->sheet->setCellValue('F4','7 - 15 th');


                $event->sheet->mergeCells('H4:I4');
                $event->sheet->setCellValue('H4','15 - 59 th');


                $event->sheet->mergeCells('J4:K4');
                $event->sheet->setCellValue('J4',' > 60 th');

                $event->sheet->mergeCells('L4:N4');
                $event->sheet->setCellValue('L4','TOTAL');

                $event->sheet->mergeCells('O3:Q3');
                // $event->sheet->mergeCells('O3:O4');
                $event->sheet->setCellValue('O3','KASUS LAMA **)');
                

                $event->sheet->mergeCells('R3:T3');
                // $event->sheet->mergeCells('O3:O4');
                $event->sheet->setCellValue('R3','JML KUNJUNGAN KASUS (JKK)');

                $event->sheet->setCellValue('D5','L');
                $event->sheet->setCellValue('E5','P');


                $event->sheet->setCellValue('F5','L');
                $event->sheet->setCellValue('G5','P');


                $event->sheet->setCellValue('H5','L');
                $event->sheet->setCellValue('I5','P');


                $event->sheet->setCellValue('J5','L');
                $event->sheet->setCellValue('K5','P');


                $event->sheet->setCellValue('L5','L');
                $event->sheet->setCellValue('M5','P');
                $event->sheet->setCellValue('N5','JML');

                $event->sheet->setCellValue('O5','L');
                $event->sheet->setCellValue('P5','P');
                $event->sheet->setCellValue('Q5','JML');
                
                $event->sheet->setCellValue('R5','L');
                $event->sheet->setCellValue('S5','P');
                $event->sheet->setCellValue('T5','JML');

                $event->sheet->setCellValue('A6','1');
                $event->sheet->setCellValue('A7','2');
                $event->sheet->setCellValue('A8','3');
                $event->sheet->setCellValue('A9','4');
                $event->sheet->setCellValue('A10','5');
                $event->sheet->setCellValue('A11','6');
                $event->sheet->setCellValue('A12','7');
                $event->sheet->setCellValue('A13','8');
                $event->sheet->setCellValue('A14','9');
                $event->sheet->setCellValue('A15','10');
                $event->sheet->setCellValue('A16','11');
                $event->sheet->setCellValue('A17','12');
                $event->sheet->setCellValue('A18','13');
                $event->sheet->setCellValue('A19','14');
                $event->sheet->setCellValue('A20','15');
                $event->sheet->setCellValue('A21','16');
                $event->sheet->setCellValue('A22','17');
                $event->sheet->setCellValue('A23','18');
                $event->sheet->setCellValue('A24','19');
                $event->sheet->setCellValue('A25','20');

                $event->sheet->setCellValue('B6','Gangguan pertumbuhan dan erupsi gigi');
                $event->sheet->setCellValue('B7','Gigi Tertanam dan Impaksi');
                $event->sheet->setCellValue('B8','Karies Gigi');
                $event->sheet->setCellValue('B9','Penyakit Jaringan Keras Gigi Lainnya');
                $event->sheet->setCellValue('B10','Penyakit Pulpa dan Jaringan Periapikal');
                $event->sheet->setCellValue('B11','Gingivitis dan Penyakit Periodontal');
                $event->sheet->setCellValue('B12','Pembesaran Gingiva');
                $event->sheet->setCellValue('B13','Anomali Dentofasial');
                $event->sheet->setCellValue('B14','Gangguan Gigi dan Jaringan Penyangga Lainnya');
                $event->sheet->setCellValue('B15','Kista Rongga Mulut');
                $event->sheet->setCellValue('B16','Penyakit Rahang Lain');
                $event->sheet->setCellValue('B17','Penyakit Kelenjar Liur');
                $event->sheet->setCellValue('B18','Stomatitis dan Lesi-lesi berhubungan');
                $event->sheet->setCellValue('B19','Angular Cheilitis');
                $event->sheet->setCellValue('B20','Penyakit Lidah');
                $event->sheet->setCellValue('B21','Kanker rongga mulut');
                $event->sheet->setCellValue('B22','Cleft palate');
                $event->sheet->setCellValue('B23','Cleft lip');
                $event->sheet->setCellValue('B24','cleft palate with cleft lip');
                $event->sheet->setCellValue('B25','Lain-lain');

                $event->sheet->setCellValue('C6','K00');
                $event->sheet->setCellValue('C7','K01');
                $event->sheet->setCellValue('C8','K02');
                $event->sheet->setCellValue('C9','K03');
                $event->sheet->setCellValue('C10','K04');
                $event->sheet->setCellValue('C11','K05');
                $event->sheet->setCellValue('C12','K06');
                $event->sheet->setCellValue('C13','K07');
                $event->sheet->setCellValue('C14','K08');
                $event->sheet->setCellValue('C15','K09');
                $event->sheet->setCellValue('C16','K10');
                $event->sheet->setCellValue('C17','K11');
                $event->sheet->setCellValue('C18','K12');
                $event->sheet->setCellValue('C19','K13');
                $event->sheet->setCellValue('C20','K14');
                $event->sheet->setCellValue('C21','C06.9');
                $event->sheet->setCellValue('C22','Q35');
                $event->sheet->setCellValue('C23','Q36');
                $event->sheet->setCellValue('C24','Q37');
                $event->sheet->setCellValue('C25','');


                // 0

                

                for ($i=0; $i < 20 ; $i++) { 
                    $event->sheet->setCellValue('D'.strval(6+$i) ,count($this->data['kasus_baru'][$i]['pria'][1]));
                    $event->sheet->setCellValue('F'.strval(6+$i) ,count($this->data['kasus_baru'][$i]['pria'][2]));
                    $event->sheet->setCellValue('H'.strval(6+$i) ,count($this->data['kasus_baru'][$i]['pria'][3]));
                    $event->sheet->setCellValue('J'.strval(6+$i) ,count($this->data['kasus_baru'][$i]['pria'][4]));

                    $event->sheet->setCellValue('E'.strval(6+$i) ,count($this->data['kasus_baru'][$i]['wanita'][1]));
                    $event->sheet->setCellValue('G'.strval(6+$i) ,count($this->data['kasus_baru'][$i]['wanita'][2]));
                    $event->sheet->setCellValue('I'.strval(6+$i) ,count($this->data['kasus_baru'][$i]['wanita'][3]));
                    $event->sheet->setCellValue('K'.strval(6+$i) ,count($this->data['kasus_baru'][$i]['wanita'][4]));

                    $event->sheet->setCellValue('L'.strval(6+$i),count($this->data['kasus_baru'][$i]['pria'][1]) + count($this->data['kasus_baru'][$i]['pria'][2]) + count($this->data['kasus_baru'][$i]['pria'][3]) + count($this->data['kasus_baru'][$i]['pria'][4]));
                    $event->sheet->setCellValue('M'.strval(6+$i),count($this->data['kasus_baru'][$i]['wanita'][1]) + count($this->data['kasus_baru'][$i]['wanita'][2]) + count($this->data['kasus_baru'][$i]['wanita'][3]) + count($this->data['kasus_baru'][$i]['wanita'][4]));

                    $event->sheet->setCellValue('N'.strval(6+$i),count($this->data['kasus_baru'][$i]['pria'][1]) + count($this->data['kasus_baru'][$i]['pria'][2]) + count($this->data['kasus_baru'][$i]['pria'][3]) + count($this->data['kasus_baru'][$i]['pria'][4]) + count($this->data['kasus_baru'][$i]['wanita'][1]) + count($this->data['kasus_baru'][$i]['wanita'][2]) + count($this->data['kasus_baru'][$i]['wanita'][3]) + count($this->data['kasus_baru'][$i]['wanita'][4]));

                }


                for ($i=0; $i < 19; $i++) { 
                   
                    $event->sheet->setCellValue('O'.strval(6+$i),count($this->data['kasus_lama'][$i]['pria'][1]) + count($this->data['kasus_lama'][$i]['pria'][2]) + count($this->data['kasus_lama'][$i]['pria'][3]) + count($this->data['kasus_lama'][$i]['pria'][4]));

                    $event->sheet->setCellValue('P'.strval(6+$i),count($this->data['kasus_lama'][$i]['wanita'][1]) + count($this->data['kasus_lama'][$i]['wanita'][2]) + count($this->data['kasus_lama'][$i]['wanita'][3]) + count($this->data['kasus_lama'][$i]['wanita'][4]));


                    $event->sheet->setCellValue('Q'.strval(6+$i),count($this->data['kasus_lama'][$i]['wanita'][1]) + count($this->data['kasus_lama'][$i]['wanita'][2]) + count($this->data['kasus_lama'][$i]['wanita'][3]) + count($this->data['kasus_lama'][$i]['wanita'][4]) + count($this->data['kasus_lama'][$i]['pria'][1]) + count($this->data['kasus_lama'][$i]['pria'][2]) + count($this->data['kasus_lama'][$i]['pria'][3]) + count($this->data['kasus_lama'][$i]['pria'][4]));

                    

                }


                for ($i=0; $i < 19; $i++) { 

                    $event->sheet->setCellValue('R'.strval(6+$i),count($this->data['kasus_baru'][$i]['pria'][1]) + count($this->data['kasus_baru'][$i]['pria'][2]) + count($this->data['kasus_baru'][$i]['pria'][3]) + count($this->data['kasus_baru'][$i]['pria'][4]) + count($this->data['kasus_lama'][$i]['pria'][1]) + count($this->data['kasus_lama'][$i]['pria'][2]) + count($this->data['kasus_lama'][$i]['pria'][3]) + count($this->data['kasus_lama'][$i]['pria'][4]));

                    $event->sheet->setCellValue('S'.strval(6+$i),count($this->data['kasus_baru'][$i]['wanita'][1]) + count($this->data['kasus_baru'][$i]['wanita'][2]) + count($this->data['kasus_baru'][$i]['wanita'][3]) + count($this->data['kasus_baru'][$i]['wanita'][4]) + count($this->data['kasus_lama'][$i]['wanita'][1]) + count($this->data['kasus_lama'][$i]['wanita'][2]) + count($this->data['kasus_lama'][$i]['wanita'][3]) + count($this->data['kasus_lama'][$i]['wanita'][4]));

                    $event->sheet->setCellValue('T'.strval(6+$i),count($this->data['kasus_baru'][$i]['wanita'][1]) + count($this->data['kasus_baru'][$i]['wanita'][2]) + count($this->data['kasus_baru'][$i]['wanita'][3]) + count($this->data['kasus_baru'][$i]['wanita'][4]) + count($this->data['kasus_lama'][$i]['wanita'][1]) + count($this->data['kasus_lama'][$i]['wanita'][2]) + count($this->data['kasus_lama'][$i]['wanita'][3]) + count($this->data['kasus_lama'][$i]['wanita'][4]) + count($this->data['kasus_baru'][$i]['pria'][2]) + count($this->data['kasus_baru'][$i]['pria'][3]) + count($this->data['kasus_baru'][$i]['pria'][4]) + count($this->data['kasus_lama'][$i]['pria'][1]) + count($this->data['kasus_lama'][$i]['pria'][2]) + count($this->data['kasus_lama'][$i]['pria'][3]) + count($this->data['kasus_lama'][$i]['pria'][4]));

                    


                }

                $event->sheet->setCellValue('R25',count($this->data['kasus_baru'][19]['pria'][1]) + count($this->data['kasus_baru'][19]['pria'][2]) + count($this->data['kasus_baru'][19]['pria'][3]) + count($this->data['kasus_baru'][19]['pria'][4]));
                $event->sheet->setCellValue('S25',count($this->data['kasus_baru'][19]['wanita'][1]) + count($this->data['kasus_baru'][19]['wanita'][2]) + count($this->data['kasus_baru'][19]['wanita'][3]) + count($this->data['kasus_baru'][19]['wanita'][4]));
                 $event->sheet->setCellValue('T25',count($this->data['kasus_baru'][19]['pria'][1]) + count($this->data['kasus_baru'][19]['pria'][2]) + count($this->data['kasus_baru'][19]['pria'][3]) + count($this->data['kasus_baru'][19]['pria'][4]) + count($this->data['kasus_baru'][19]['wanita'][1]) + count($this->data['kasus_baru'][19]['wanita'][2]) + count($this->data['kasus_baru'][19]['wanita'][3]) + count($this->data['kasus_baru'][19]['wanita'][4]));

                $event->sheet->setCellValue('O25','-');
                $event->sheet->setCellValue('P25','-');
                $event->sheet->setCellValue('Q25','-');


                // $event->sheet->setCellValue('O25','-');
                // $event->sheet->setCellValue('P25','-');
                // $event->sheet->setCellValue('Q25','-');


                $event->sheet->setCellValue('A27','*)');
                $event->sheet->mergeCells('B27:N27');
                $event->sheet->setCellValue('B27','KASUS BARU : kasus yang datang berobat untuk pertama kalinya pada sakit tersebut');

                $event->sheet->setCellValue('A28','**)');
                $event->sheet->mergeCells('B28:N28');
                $event->sheet->setCellValue('B28','KASUS LAMA : kasus yang datang berobat untuk kedua kalinya atau lebih pada episode sakit yang sama dengan berobat pertama');

                $event->sheet->setCellValue('A29','***)');
                $event->sheet->mergeCells('B29:N29');
                $event->sheet->setCellValue('B29','JUMLAH KUNJUNGAN KASUS : merupakan penjumlahan kasus lama dan kasus baru');



                

                

                



                




                
                


                


                    

                // $event->sheet->setCellValue('E:','TAHUN');
                // $event->sheet->setColumnFormat('A1','asdasd');

                // $event->sheet->getStyle('A2:E2')->applyFromArray($styleArray);
                // $event->sheet->setCellValue('A'.$this->count,$this->jumlah);
                // $event->sheet->getStyle('A'.$this->count.':E'.$this->count)->applyFromArray($jumlahStyle);

                // $event->sheet->setCellValue('D1',"Tahun $this->tahun Bulan $this->bulan");

                // $event->sheet->setCellValue('D'.($this->count+3),'Tertanda');
                // $event->sheet->getStyle('D'.($this->count+3))->applyFromArray($tertandaStyle);

                // $event->sheet->setCellValue('D'.($this->count+8),'Hardiansyah, S.H');
                // $event->sheet->getStyle('D'.($this->count+8))->applyFromArray($namaStyle);

                // $event->sheet->setCellValue('D'.($this->count+9),'Direktur Utama');
                // $event->sheet->getStyle('D'.($this->count+8))->applyFromArray($tertandaStyle);
            },
                        
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return [];
    }

    public function headings(): array
    {
        return [
            ['LAPORAN KASUS PENYAKIT PENYAKIT GIGI DAN MULUT'],
            ['JUMLAH',
            'KATEGORI',
            'TANGGAL',
            'KETERANGAN',
            'TIPE']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // $sheet->setBorder('A1', 'solid');

        return [
            // Style the first row as bold text.
            1   => ['font' => ['bold' => true,'size' => 20], 'border' => ['solid']],
            // 2    => ['font' => ['bold' => true,'size' => 15], 'border' => ['solid']],
            // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A1' => array(
                'width' => 60,
            ),
            'B' => 5,
            'C' => 5,
            'D' => 5,
            'E' => 5            
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'PT. Garuda Karya Medika',
            'lastModifiedBy' => 'Farhan',
            'title'          => 'Laporan Keuangan',
            'description'    => 'Laporan Keuangan Perusahaan',
            'subject'        => 'Laporan',
            'keywords'       => 'laporan,export,spreadsheet',
            'category'       => 'laporan',
            'manager'        => 'Safari Creative',
            'company'        => 'PT. Garuda Karya Medika',
        ];
    }

    public function laporanData($tahun, $bulan) {
        $tahun = $tahun == "0" ? Carbon::now()->format('Y') : $tahun;
        $bulan = $bulan == "0" ? Carbon::now()->format('m') : $bulan;

        $rekamMedis = RekamMedis::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->get();

        $data = [];

        

        $data['kasus_baru'] = [];
       

        for ($i=0; $i < 20 ; $i++) { 
            $data['kasus_baru'][$i] =[];
            $data['kasus_baru'][$i]['pria'] = [];
            $data['kasus_baru'][$i]['pria'][1] = [];
            $data['kasus_baru'][$i]['pria'][2] = [];
            $data['kasus_baru'][$i]['pria'][3] = [];
            $data['kasus_baru'][$i]['pria'][4] = [];

            $data['kasus_baru'][$i]['wanita'] = [];
            $data['kasus_baru'][$i]['wanita'][1] = [];
            $data['kasus_baru'][$i]['wanita'][2] = [];
            $data['kasus_baru'][$i]['wanita'][3] = [];
            $data['kasus_baru'][$i]['wanita'][4] = [];
        }


        $data['kasus_lama'] = [];

        for ($i=0; $i < 20 ; $i++) { 
            $data['kasus_lama'][$i] =[];
            $data['kasus_lama'][$i]['pria'] = [];
            $data['kasus_lama'][$i]['pria'][1] = [];
            $data['kasus_lama'][$i]['pria'][2] = [];
            $data['kasus_lama'][$i]['pria'][3] = [];
            $data['kasus_lama'][$i]['pria'][4] = [];


            $data['kasus_lama'][$i]['wanita'] = [];
            $data['kasus_lama'][$i]['wanita'][1] = [];
            $data['kasus_lama'][$i]['wanita'][2] = [];
            $data['kasus_lama'][$i]['wanita'][3] = [];
            $data['kasus_lama'][$i]['wanita'][4] = [];
        }




        $diagnosaPria['data'] = [];
        foreach ($rekamMedis as $row) {
            $pemeriksaanGigi = PemeriksaanGigi::where('id_rm',$row->id)->get();
            foreach ($pemeriksaanGigi as $rowPemeriksaan) {
                $identitas = Identitas::where('no_rm',$rowPemeriksaan->no_rm)->get();
                foreach ($identitas as $rowIden) {
                    if($rowIden->jenis_kelamin == 1)
                    {

                        $umur = Carbon::parse($rowIden['tanggal_lahir'])->age;

                        if($umur <= 7) {


                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['pria'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['pria'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['pria'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['pria'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['pria'][1],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['pria'][1],'baru');
                                // array_push($data['kasus_lama'][19]['pria'][1],'baru');
                            }

                            
                        

                        }else if($umur > 7 && $umur <= 15) {
                            
                           
                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['pria'][3],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['pria'][3],'baru');
                                array_push($data['kasus_lama'][19]['pria'][3],'baru');
                            }

                            
                        

                        }else if($umur > 15 && $umur < 59) {
                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['pria'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['pria'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['pria'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['pria'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['pria'][2],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['pria'][2],'baru');
                                array_push($data['kasus_lama'][19]['pria'][2],'baru');
                            }

                            
                        }else if($umur >= 60) {

                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['pria'][3],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['pria'][3],'baru');
                                array_push($data['kasus_lama'][19]['pria'][3],'baru');
                            }

                            
                        
                        }
                        

                        // array_push($data['umur'],$umur);
                        // $totalLaki += 1;

                        // array_push($diagnosaPria['data'], $rowPemeriksaan->diagnosa);


                        


                    }else{
                        $umur = Carbon::parse($rowIden['tanggal_lahir'])->age;

                        if($umur <= 7) {


                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['wanita'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['wanita'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['wanita'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['wanita'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['wanita'][1],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['wanita'][1],'baru');
                                array_push($data['kasus_lama'][19]['wanita'][1],'baru');
                            }

                            


                        }else if($umur > 7 && $umur <= 15) {
                            
                        
                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['wanita'][3],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['wanita'][3],'baru');
                                array_push($data['kasus_lama'][19]['wanita'][3],'baru');
                            }

                            


                        }else if($umur > 15 && $umur < 59) {
                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['wanita'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['wanita'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['wanita'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['wanita'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['wanita'][2],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['wanita'][2],'baru');
                                array_push($data['kasus_lama'][19]['wanita'][2],'baru');
                            }

                            
                        }else if($umur >= 60) {

                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['wanita'][3],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['wanita'][3],'baru');
                            }

                            

                        }


                        // array_push($data['umur'],$umur);
                        // $totalLaki += 1;

                        // array_push($diagnosawanita['data'], $rowPemeriksaan->diagnosa);






                    }
                }

            }
            

        }

        return $data;

    }
}