<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    public function downloadFormatExcelImport(){
        $datarole = DB::table('roles')->get();
        $datarolecount = DB::table('roles')->count('*');
        $dataOrg = DB::table('organizations')->get();
        $dataOrgcount = DB::table('organizations')->count('*');
        $dataPos = DB::table('job_positions')->get();
        $dataPoscount = DB::table('job_positions')->count('*');
        $dataLvl = DB::table('job_levels')->get();
        $dataLvlcount = DB::table('job_levels')->count('*');
        $dataUsr = DB::table('users')->get();
        $dataUsrcount = DB::table('users')->count('*');
        $dataCom = DB::table('companies')->get();
        $dataComcount = DB::table('companies')->count('*');
        $dataBrc = DB::table('branches')->get();
        $dataBrccount = DB::table('branches')->count('*');
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('E-SAS')
            ->setLastModifiedBy('Anothers')
            ->setTitle('Users E-SAS Import')
            ->setDescription('Sample Excel file');
        // Set borders around the table range to create a table effect
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        // Add some data
        $sheet1 = $spreadsheet->getActiveSheet();

        $sheet1->mergeCells('A1:D1');
        $sheet1->setCellValue('A1', 'PERHATIKAN ATURAN PENGISIAN ROW IMPORT DATA PADA TABEL INI SEBELUM ANDA MEMUAT DATA IMPORT PADA SHEET "DATA USERS IMPORT", PASTIKAN SEMUA KOLOM SESUAI DENGAN ATURAN KAMI!');

        $sheet1->setCellValue('A2', 'COLUMNAME');
        $sheet1->setCellValue('B2', 'DESCRIPTION');
        $sheet1->setCellValue('C2', 'COLUMNAME');
        $sheet1->setCellValue('D2', 'DESCRIPTION');

        $cellName = 'A';
        $columnAB = [
            ['colname' => 'name', 'desc' => '(string), isi dengan nama lengkap user'],
            ['colname' => 'role_id', 'desc' => '(integer), isi dengan id role yang terdapat pada sheet "data role"'],
            ['colname' => 'nik', 'desc' => '(string), isi dengan nik user yang bersifat unique (data ini akan dipakai untuk authorisasi login)'],
            ['colname' => 'email', 'desc' => '(string), isi dengan alamat email aktif user (data ini akan dipakai untuk authorisasi login)'],
            ['colname' => 'password', 'desc' => '(string), isi dengan kata sandi user (data ini akan dipakai untuk authorisasi login)'],
            ['colname' => 'email_verified_at', 'desc' => '(datetime), isi dengan tanggal dengan format "Y-m-d H:i:s"'],
            ['colname' => 'phone', 'desc' => '(integer), isi dengan nomor telpon user (contoh:08xxxxxxxx)'],
            ['colname' => 'placebirth', 'desc' => '(string), isi dengan tempat lahir user (contoh:Tangerang)'],
            ['colname' => 'datebirth', 'desc' => '(date), isi dengan tanggal dengan format "Y-m-d" (contoh:2024-01-01)'],
            ['colname' => 'gender', 'desc' => '(enum), isi dengan inisial "m" untuk male & "w" untuk woman (contoh:m)'],
            ['colname' => 'blood', 'desc' => '(enum), isi dengan inisial "a", "b", "o", "ab" (contoh:a)'],
            ['colname' => 'marital_status', 'desc' => '(enum), isi dengan pilihan "single", "marriade", "widow", "widower" (contoh:single)'],
            ['colname' => 'religion', 'desc' => '(enum), isi dengan pilihan "islam","protestant","catholic","hindu","buddha","khonghucu" (contoh:islam)'],
            ['colname' => 'idtype', 'desc' => '(enum), isi dengan pilihan "ktp","passport" (contoh:ktp)'],
            ['colname' => 'idnumber', 'desc' => '(integer), isi dengan nomor user (contoh:9xxxxxxxxxxxxxx)'],
            ['colname' => 'bpjs_kesehatan_date', 'desc' => '(date), isi dengan tanggal dengan format "Y-m-d" (contoh:2024-01-01)'],
            ['colname' => 'bpjs_kesehatan_cost', 'desc' => '(integer), isi dengan nomor user (contoh:9xxxxxxxxxxxxxx)'],
            ['colname' => 'jht_cost', 'desc' => '(date), isi dengan tanggal dengan format "Y-m-d" (contoh:2024-01-01)'],
            ['colname' => 'jaminan_pensiun_cost', 'desc' => '(integer), isi dengan nomor user (contoh:9xxxxxxxxxxxxxx)'],
            ['colname' => 'jaminan_pensiun_date', 'desc' => '(date), isi dengan tanggal dengan format "Y-m-d" (contoh:2024-01-01)'],
            ['colname' => 'organization_id', 'desc' => '(integer), isi dengan id organisasi/dept yang terdapat pada sheet "data dept"'],
            ['colname' => 'job_position_id', 'desc' => '(integer), isi dengan id position yang terdapat pada sheet "data position"'],
            ['colname' => 'job_level_id', 'desc' => '(integer), isi dengan id level yang terdapat pada sheet "data level"'],
            ['colname' => 'approval_line', 'desc' => '(integer), isi dengan id user yang terdapat pada sheet "data user"'],
            ['colname' => 'approval_manager', 'desc' => '(integer), isi dengan id user yang terdapat pada sheet "data user"'],
            ['colname' => 'company_id', 'desc' => '(integer), isi dengan id branch yang terdapat pada sheet "data companies"'],
            ['colname' => 'branch_id', 'desc' => '(integer), isi dengan id branch yang terdapat pada sheet "data branch"'],
            ['colname' => 'status', 'desc' => '(enum), isi dengan pilihan "contract","permanent","magang","last daily" (contoh:contract)'],
            ['colname' => 'join_date', 'desc' => '(date), isi dengan tanggal dengan format "Y-m-d" (contoh:2024-01-01)'],
            ['colname' => 'sign_date', 'desc' => '(date), isi dengan tanggal dengan format "Y-m-d" (contoh:2024-01-01)'],
            ['colname' => 'basic_salary', 'desc' => '(integer), isi dengan nomor user (contoh:9xxxxxxxxxxxxxx)'],
        ];
        $columnCD = [
            ['colname' => 'idexpired', 'desc' => '(date), isi dengan tanggal dengan format "Y-m-d" (contoh:2024-01-01)'],
            ['colname' => 'ispermanent', 'desc' => '(enum), isi dengan pilihan "true","false" (contoh:true)'],
            ['colname' => 'postalcode', 'desc' => '(integer), isi dengan nomor kodepos user (contoh:14xxx)'],
            ['colname' => 'citizen_id_address', 'desc' => '(string), isi dengan alamat lengkap user'],
            ['colname' => 'use_as_residential', 'desc' => '(enum), isi dengan pilihan "true","false" (contoh:true)'],
            ['colname' => 'residential_address', 'desc' => '(string), isi dengan alamat lengkap user'],
            ['colname' => 'bank_name', 'desc' => '(string), isi dengan nama bank user'],
            ['colname' => 'bank_account', 'desc' => '(integer), isi dengan nomor rekening user'],
            ['colname' => 'bank_account_holder', 'desc' => '(string), isi nama pemilik akun bank user'],
            ['colname' => 'bpjs_ketenagakerjaan', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'npp_bpjs_ketenagakerjaan', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'bpjs_ketenagakerjaan_date', 'desc' => '(date), isi dengan tanggal dengan format "Y-m-d" (contoh:2024-01-01)'],
            ['colname' => 'bpjs_kesehatan', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'bpjs_kesehatan_family', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'salary_type', 'desc' => '(enum), isi dengan pilihan "Monthly", "Weakly", "Dayly" (contoh:Dayly)'],
            ['colname' => 'payment_schedule', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'prorate_settings', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'overtime_settings', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'cost_center', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'cost_center_category', 'desc' => '(string), kolom ini tidak boleh kosong!'],
            ['colname' => 'currency', 'desc' => '(string), isi dengan nama mata uang (contoh:Rupiah)!'],
            ['colname' => 'npwp_15_digit_old', 'desc' => '(integer), isi dengan nomor (contoh:9xxxxxxxxxxxxxx)'],
            ['colname' => 'npwp_16_digit_new', 'desc' => '(integer), isi dengan nomor (contoh:9xxxxxxxxxxxxxxx)'],
            ['colname' => 'ptkp_status', 'desc' => '(enum), isi dengan pilihan "TK0","TK1","TK2","TK3","K0","K1","K2","K3","K/I/0","K/I/1","K/I/2","K/I/3" (contoh:TK0)'],
            ['colname' => 'tax_method', 'desc' => '(enum), isi dengan pilihan "gross" (contoh:gross)'],
            ['colname' => 'tax_salary', 'desc' => '(enum), isi dengan pilihan "taxable" (contoh:taxable)'],
            ['colname' => 'emp_tax_status', 'desc' => '(enum), isi dengan pilihan "permanent", "contract", "last-daily" (contoh:permanent)'],
            ['colname' => 'beginning_netto', 'desc' => '(integer), isi dengan nomor (contoh:9xxxxxxxxxxxxxx)'],
            ['colname' => 'pph21_paid', 'desc' => '(integer), isi dengan nomor (contoh:9xxxxxxxxxxxxxx)'],
        ];
        $sheet1rowAB = 3;
        foreach ($columnAB as $k) {
            $sheet1->setCellValue('A'.$sheet1rowAB, $k['colname']);
            $sheet1->setCellValue('B'.$sheet1rowAB, $k['desc']);
            $sheet1rowAB++;
        }
        $sheet1rowCD = 3;
        foreach ($columnCD as $k) {
            $sheet1->setCellValue('C'.$sheet1rowCD, $k['colname']);
            $sheet1->setCellValue('D'.$sheet1rowCD, $k['desc']);
            $sheet1rowCD++;
        }

        // Style the cells
        $style = $sheet1->getStyle('A1:D1');
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet1->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('d1ebff');
        $style = $sheet1->getStyle('A1:D1');
        $font = $style->getFont();
        $font->setBold(true);
        $sheet1->getStyle('A2:D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet1->getStyle('A2:D2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('7c9288');
        $sheet1->getStyle('A3:D32')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('dafffb');
        $sheet1->getStyle('A1:D32')->applyFromArray($styleArray);


        $result = array_merge($columnAB, $columnCD);
        $headerName = [];
        foreach ($result as $key) {
            array_push($headerName, $key['colname']);
        }

        $sheet2 = $spreadsheet->createSheet();
        $cellName = 'A';
        foreach ($headerName as $header) {
            $sheet2->setCellValue($cellName . '1', $header);
            $cellName++;
        }
        $optionsrole = [];
        $optionsOrg = [];
        $optionsPos = [];
        $optionsLvl = [];
        $optionsUsr = [];
        $optionsCom = [];
        $optionsBrc = [];
        foreach ($datarole as $key =>$value) {
            array_push($optionsrole, (String)$value->id);
        }
        foreach ($dataOrg as $key =>$value) {
            array_push($optionsOrg, (String)$value->id);
        }
        foreach ($dataPos as $key =>$value) {
            array_push($optionsPos, (String)$value->id);
        }
        foreach ($dataLvl as $key =>$value) {
            array_push($optionsLvl, (String)$value->id);
        }
        foreach ($dataUsr as $key =>$value) {
            array_push($optionsUsr, (String)$value->id);
        }
        foreach ($dataCom as $key =>$value) {
            array_push($optionsCom, (String)$value->id);
        }
        foreach ($dataBrc as $key =>$value) {
            array_push($optionsBrc, (String)$value->id);
        }

        $strRole1="='Data Roles'!";
        $strRole2='$A$2:$A$'.count($optionsrole)+1;
        $strOrg1="='Data Organization'!";
        $strOrg2='$A$2:$A$'.count($optionsOrg)+1;
        $strPos1="='Data Position'!";
        $strPos2='$A$2:$A$'.count($optionsPos)+1;
        $strLvl1="='Data Level'!";
        $strLvl2='$A$2:$A$'.count($optionsLvl)+1;
        $strUsr1="='Data Users Approval'!";
        $strUsr2='$A$2:$A$'.count($optionsUsr)+1;
        $strCom1="='Data Companies'!";
        $strCom2='$A$2:$A$'.count($optionsCom)+1;
        $strBrc1="='Data Branch'!";
        $strBrc2='$A$2:$A$'.count($optionsBrc)+1;

        $sheet2->getCell('B2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1("$strRole1$strRole2");
        $sheet2->getCell('J2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"m,w"');
        $sheet2->getCell('K2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"a, b, o, ab"');
        $sheet2->getCell('L2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"single, marriade, widow, widower"');
        $sheet2->getCell('M2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"islam,protestant,catholic,hindu,buddha,khonghucu"');
        $sheet2->getCell('N2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"ktp,passport"');
        $sheet2->getCell('U2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1("$strOrg1
        $strOrg2");
        $sheet2->getCell('V2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1("$strPos1
        $strPos2");
        $sheet2->getCell('W2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1("$strLvl1
        $strLvl2");
        $sheet2->getCell('X2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1("$strUsr1
        $strUsr2");
        $sheet2->getCell('Y2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1("$strUsr1
        $strUsr2");
        $sheet2->getCell('Z2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1("$strCom1
        $strCom2");
        $sheet2->getCell('AA2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1("$strBrc1
        $strBrc2");
        $sheet2->getCell('AB2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"contract,permanent,magang,last daily"');
        $sheet2->getCell('AG2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"true,false"');
        $sheet2->getCell('AJ2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"true,false"');
        $sheet2->getCell('AT2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"Monthly, Weakly, Dayly"');
        $sheet2->getCell('BC2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"TK0,TK1,TK2,TK3,K0,K1,K2,K3,K/I/0,K/I/1,K/I/2,K/I/3"');
        $sheet2->getCell('BD2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"gross"');
        $sheet2->getCell('BE2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"taxable"');
        $sheet2->getCell('BF2')->getDataValidation()->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowInputMessage(true)->setShowErrorMessage(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list.')->setPromptTitle('Pick from list')->setPrompt('Please pick a value from the drop-down list.')->setFormula1('"permanent, contract, last-daily"');

        // Style the cells
        $sheet2->getStyle('A1:Bh1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('A1:Bh1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('7c9288');
        $sheet2->getStyle('A1:Bh33')->applyFromArray($styleArray);
        $sheet2->setTitle('Data Users Import');

        $sheet3 = $spreadsheet->createSheet();
        $sheet3Header = ['ID', 'NAME', 'GUARDNAME', 'CREATED', 'UPDATED'];
        $sheet3cellName = 'A';
        foreach ($sheet3Header as $header) {
            $sheet3->setCellValue($sheet3cellName . '1', $header);
            $sheet3cellName++;
        }
        $row = 2; // Start from row 2 (after the headers)
        foreach ($datarole as $item) {
            $column = 'A'; // Reset column position for each row
            
            foreach ($item as $value) {
                $sheet3->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        $tableRangeSheet3 = 'A1:E'.((int)$datarolecount + 1);
        $sheet3->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet3->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('079246');
        $sheet3->getStyle($tableRangeSheet3)->applyFromArray($styleArray);
        $sheet3->setTitle('Data Roles');

        $sheet4 = $spreadsheet->createSheet();
        $sheet4Header = ['ID', 'NAME', 'DESCRIPTION', 'CREATED', 'UPDATED'];
        $sheet4cellName = 'A';
        foreach ($sheet4Header as $header) {
            $sheet4->setCellValue($sheet4cellName . '1', $header);
            $sheet4cellName++;
        }
        
        $row = 2; // Start from row 2 (after the headers)
        foreach ($dataOrg as $item) {
            $column = 'A'; // Reset column position for each row
            
            foreach ($item as $value) {
                $sheet4->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        $tableRangeSheet4 = 'A1:E'.((int)$dataOrgcount + 1);
        $sheet4->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet4->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('079246');
        $sheet4->getStyle($tableRangeSheet4)->applyFromArray($styleArray);
        $sheet4->setTitle('Data Organization');

        $sheet5 = $spreadsheet->createSheet();
        $sheet5Header = ['ID', 'NAME', 'DESCRIPTION', 'CREATED', 'UPDATED'];
        $sheet5cellName = 'A';
        foreach ($sheet5Header as $header) {
            $sheet5->setCellValue($sheet5cellName . '1', $header);
            $sheet5cellName++;
        }
        
        $row = 2; // Start from row 2 (after the headers)
        foreach ($dataPos as $item) {
            $column = 'A'; // Reset column position for each row
            
            foreach ($item as $value) {
                $sheet5->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        $tableRangeSheet5 = 'A1:E'.((int)$dataPoscount + 1);
        $sheet5->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet5->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('079246');
        $sheet5->getStyle($tableRangeSheet5)->applyFromArray($styleArray);
        $sheet5->setTitle('Data Position');

        $sheet6 = $spreadsheet->createSheet();
        $sheet6Header = ['ID', 'NAME', 'DESCRIPTION', 'CREATED', 'UPDATED'];
        $sheet6cellName = 'A';
        foreach ($sheet6Header as $header) {
            $sheet6->setCellValue($sheet6cellName . '1', $header);
            $sheet6cellName++;
        }
        
        $row = 2; // Start from row 2 (after the headers)
        foreach ($dataLvl as $item) {
            $column = 'A'; // Reset column position for each row
            
            foreach ($item as $value) {
                $sheet6->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        $tableRangeSheet6 = 'A1:E'.((int)$dataLvlcount + 1);
        $sheet6->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet6->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('079246');
        $sheet6->getStyle($tableRangeSheet6)->applyFromArray($styleArray);
        $sheet6->setTitle('Data Level');

        $sheet7 = $spreadsheet->createSheet();
        $sheet7Header = ['ID', 'NAME', 'NIK', 'EMAIL', 'PHONE', 'PLACEBIRTH', 'DATEBIRTH', 'GENDER', 'BLOODTYPE', 'MARITAL STATUS', 'RELIGION'];
        $sheet7cellName = 'A';
        foreach ($sheet7Header as $header) {
            $sheet7->setCellValue($sheet7cellName . '1', $header);
            $sheet7cellName++;
        }
        
        $row = 2; // Start from row 2 (after the headers)
        foreach ($dataUsr as $item) {
            $column = 'A'; // Reset column position for each row
            $array = array('password', 'email_verified_at', 'image', 'remember_token', 'created_at', 'updated_at', 'deleted_at');
            foreach ($array as $key) {
                unset($item->$key);
            }
            foreach ($item as $value) {
                $sheet7->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        $tableRangeSheet7 = 'A1:K'.((int)$dataUsrcount + 1);
        $sheet7->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet7->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('079246');
        $sheet7->getStyle($tableRangeSheet7)->applyFromArray($styleArray);
        $sheet7->setTitle('Data Users Approval');

        $sheet8 = $spreadsheet->createSheet();
        $sheet8Header = ['ID', 'NAME', 'LATITUDE', 'LONGITUDE', 'ADDRESS', 'CREATEDAT', 'UPDATEDAT'];
        $sheet8cellName = 'A';
        foreach ($sheet8Header as $header) {
            $sheet8->setCellValue($sheet8cellName . '1', $header);
            $sheet8cellName++;
        }
        
        $row = 2; // Start from row 2 (after the headers)
        foreach ($dataCom as $item) {
            $column = 'A'; // Reset column position for each row
            
            foreach ($item as $value) {
                $sheet8->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        $tableRangeSheet8 = 'A1:G'.((int)$dataComcount + 1);
        $sheet8->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet8->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('079246');
        $sheet8->getStyle($tableRangeSheet8)->applyFromArray($styleArray);
        $sheet8->setTitle('Data Companies');

        $sheet9 = $spreadsheet->createSheet();
        $sheet9Header = ['ID', 'NAME', 'LATITUDE', 'LONGITUDE', 'ADDRESS', 'CREATEDAT', 'UPDATEDAT'];
        $sheet9cellName = 'A';
        foreach ($sheet9Header as $header) {
            $sheet9->setCellValue($sheet9cellName . '1', $header);
            $sheet9cellName++;
        }
        
        $row = 2; // Start from row 2 (after the headers)
        foreach ($dataBrc as $item) {
            $column = 'A'; // Reset column position for each row
            
            foreach ($item as $value) {
                $sheet9->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        $tableRangeSheet9 = 'A1:G'.((int)$dataBrccount + 1);
        $sheet9->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet9->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('079246');
        $sheet9->getStyle($tableRangeSheet9)->applyFromArray($styleArray);
        $sheet9->setTitle('Data Branch');

        // Save the spreadsheet
        // branch
        $writer = new Xlsx($spreadsheet);
        $filename = date('YmdHis').'users_import.xlsx';
        $writer->save($filename);

        // // Return a response to download the generated Excel file
        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function importFormatExcelImport($data){
        try {
            DB::beginTransaction();
            foreach ($data as $item) {
                if ($this->validateImport($item['name']) && $this->validateImport($item['nik']) && $this->validateImport($item['email']) && $this->validateImport($item['password']) && $this->validateImport($item['phone']) && $this->validateImport($item['placebirth']) && $this->validateImport($item['datebirth']) && $this->validateImport($item['gender']) && $this->validateImport($item['blood']) && $this->validateImport($item['marital_status']) && $this->validateImport($item['religion']) && $this->validateImport($item['role_id'])) {
                    $u = User::updateOrCreate(
                        [
                            'nik' => $item['nik'],
                            'email' => $item['email'],
                        ],
                        [
                            'name' => $item['name'],
                            'email' => $item['email'],
                            'email_verified_at' => Carbon::createFromTimestamp($item['email_verified_at']),
                            'password' => bcrypt($item['password']),
                            'phone' => $item['phone'],
                            'placebirth' => $item['placebirth'],
                            'datebirth' => Carbon::createFromTimestamp($item['datebirth'])->toDateString(),
                            'gender' => $item['gender'],
                            'blood' => $item['blood'],
                            'marital_status' => $item['marital_status'],
                            'religion' => $item['religion'],
                            'image' => 'default-avatar.png',
                        ]
                    );
                    
                    $u->assignRole($item['role_id']);
    
                    $u->address()->updateOrCreate(
                        ['user_id' => $u->id],
                        [
                            'idtype' => $item['idtype'] ?? 'ktp',
                            'idnumber' => $item['idnumber'],
                            'idexpired' => Carbon::createFromTimestamp($item['idexpired'])->toDateString(),
                            'ispermanent' => $item['ispermanent'],
                            'postalcode' => $item['postalcode'],
                            'citizen_id_address' => $item['citizen_id_address'],
                            'use_as_residential' => $item['use_as_residential'],
                            'residential_address' => $item['residential_address'],
                        ]
                    );
    
                    $u->bank()->updateOrCreate(
                        ['user_id' => $u->id],
                        [
                            'bank_name' => $item['bank_name'],
                            'bank_account' => $item['bank_account'],
                            'bank_account_holder' => $item['bank_account_holder'],
                        ]
                    );
        
                    $u->bpjs()->updateOrCreate(
                        ['user_id' => $u->id],
                        [
                            'bpjs_ketenagakerjaan'=>$item['bpjs_ketenagakerjaan'],
                            'npp_bpjs_ketenagakerjaan'=>$item['npp_bpjs_ketenagakerjaan'],
                            'bpjs_ketenagakerjaan_date'=>Carbon::createFromTimestamp($item['bpjs_ketenagakerjaan_date'])->toDateString(),
                            'bpjs_kesehatan'=>$item['bpjs_kesehatan'],
                            'bpjs_kesehatan_family'=>$item['bpjs_kesehatan_family'],
                            'bpjs_kesehatan_date'=>Carbon::createFromTimestamp($item['bpjs_kesehatan_date'])->toDateString(),
                            'bpjs_kesehatan_cost'=>$item['bpjs_kesehatan_cost'],
                            'jht_cost'=>Carbon::createFromTimestamp($item['jht_cost'])->toDateString(),
                            'jaminan_pensiun_cost'=>$item['jaminan_pensiun_cost'],
                            'jaminan_pensiun_date'=>Carbon::createFromTimestamp($item['jaminan_pensiun_date'])->toDateString(),
                        ]
                    );
    
                    $u->employe()->updateOrCreate(
                        ['user_id' => $u->id],
                        [
                            'organization_id'=>$item['organization_id'],
                            'job_position_id'=>$item['job_position_id'],
                            'job_level_id'=>$item['job_level_id'],
                            'approval_line'=>$item['approval_line'],
                            'approval_manager'=>$item['approval_manager'],
                            'company_id'=>$item['company_id'],
                            'branch_id'=>$item['branch_id'],
                            'status'=>$item['status'],
                            'join_date'=>Carbon::createFromTimestamp($item['join_date'])->toDateString(),
                            'sign_date'=>Carbon::createFromTimestamp($item['sign_date'])->toDateString(),
                        ]
                    );
        
                    $u->salary()->updateOrCreate(
                        ['user_id' => $u->id],
                        [
                            'basic_salary'=>$item['basic_salary'],
                            'salary_type'=>$item['salary_type'],
                            'payment_schedule'=>$item['payment_schedule'],
                            'prorate_settings'=>$item['prorate_settings'],
                            'overtime_settings'=>$item['overtime_settings'],
                            'cost_center'=>$item['cost_center'],
                            'cost_center_category'=>$item['cost_center_category'],
                            'currency'=>$item['currency'],
                        ]
                    );
        
                    $u->tax_config()->updateOrCreate(
                        ['user_id' => $u->id],
                        [
                            'npwp_15_digit_old'=>$item['npwp_15_digit_old'],
                            'npwp_16_digit_new'=>$item['npwp_16_digit_new'],
                            'ptkp_status'=>$item['ptkp_status'],
                            'tax_method'=>$item['tax_method'],
                            'tax_salary'=>$item['tax_salary'],
                            'emp_tax_status'=>$item['emp_tax_status'],
                            'beginning_netto'=>$item['beginning_netto'],
                            'pph21_paid'=>$item['pph21_paid'],
                        ]
                    );
                }
            }
            DB::commit();
            return 'success import!';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function validateImport($input){
        if (!isset($input) || empty($input)) {
            return false;
        }elseif($input === null) {
            return false;
        }else{
            return true;
        }
    }
}
