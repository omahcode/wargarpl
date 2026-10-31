<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Student;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\ClassMember;
use App\Models\SchoolYear;
use App\Models\Semester;

class ImportExportController extends Controller
{
    public function showStudentImportUi()
    {
        $classes = SchoolClass::where('status', true)->get();
        $years = Student::select('entry_year')->distinct()->orderBy('entry_year', 'desc')->pluck('entry_year')->toArray();

        return view('admin.students.import', compact('classes', 'years'));
    }

    public function showEmployeeImportUi()
    {
        return view('admin.employees.import');
    }

    public function previewImport(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $request->validate([
            'excel_file' => 'required|file|max:25600',
            'import_type' => 'required|in:siswa,guru,tendik',
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.max' => 'Ukuran file maksimal 25MB.',
            'import_type.required' => 'Tipe import wajib dipilih.',
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            return back()->with('error', 'Format file harus berupa Excel (.xlsx, .xls) atau CSV.');
        }

        $filePath = $file->getRealPath();
        $importType = $request->input('import_type');

        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $validRows = [];
            $errorRows = [];
            $skippedCount = 0;
            
            if ($importType === 'siswa') {
                // Detect header row dynamically (Standard Dapodik has headers at Row 5)
                $headerRow = 5;
                for ($r = 1; $r <= 6; $r++) {
                    for ($c = 'A'; $c <= 'Z'; $c++) {
                        $val = strtolower(trim((string)$sheet->getCell($c . $r)->getValue()));
                        if (in_array($val, ['nama', 'nipd', 'nis', 'nama lengkap', 'nama peserta didik'])) {
                            $headerRow = $r;
                            break 2;
                        }
                    }
                }

                // Map columns based on detected header row
                $highestCol = $sheet->getHighestColumn();
                $highestColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);
                $colMap = [];

                for ($c = 1; $c <= $highestColIdx; $c++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                    $h = strtolower(trim((string)$sheet->getCell($colLetter . $headerRow)->getValue()));
                    if (empty($h)) continue;

                    if ($h === 'nama' || str_contains($h, 'nama peserta didik') || str_contains($h, 'nama siswa') || $h === 'nama lengkap') {
                        $colMap['nama'] = $colLetter;
                    } elseif ($h === 'nipd' || $h === 'nis' || str_contains($h, 'nomor induk')) {
                        $colMap['nis'] = $colLetter;
                    } elseif ($h === 'jk' || str_contains($h, 'jenis kelamin')) {
                        $colMap['gender'] = $colLetter;
                    } elseif ($h === 'nisn') {
                        $colMap['nisn'] = $colLetter;
                    } elseif (str_contains($h, 'tempat lahir')) {
                        $colMap['birth_place'] = $colLetter;
                    } elseif (str_contains($h, 'tanggal lahir')) {
                        $colMap['birth_date'] = $colLetter;
                    } elseif ($h === 'nik' || str_contains($h, 'nik')) {
                        $colMap['nik'] = $colLetter;
                    } elseif (str_contains($h, 'agama')) {
                        $colMap['religion'] = $colLetter;
                    } elseif (str_contains($h, 'alamat')) {
                        $colMap['address'] = $colLetter;
                    } elseif ($h === 'rt') {
                        $colMap['rt'] = $colLetter;
                    } elseif ($h === 'rw') {
                        $colMap['rw'] = $colLetter;
                    } elseif (str_contains($h, 'kelurahan') || str_contains($h, 'desa')) {
                        $colMap['village'] = $colLetter;
                    } elseif (str_contains($h, 'kecamatan')) {
                        $colMap['district'] = $colLetter;
                    } elseif (str_contains($h, 'kode pos')) {
                        $colMap['postal_code'] = $colLetter;
                    } elseif ($h === 'telepon' || str_contains($h, 'telp')) {
                        $colMap['phone'] = $colLetter;
                    } elseif ($h === 'hp' || str_contains($h, 'handphone') || str_contains($h, 'wa')) {
                        $colMap['whatsapp'] = $colLetter;
                    } elseif (str_contains($h, 'mail')) {
                        $colMap['email'] = $colLetter;
                    } elseif (str_contains($h, 'nama ayah')) {
                        $colMap['father_name'] = $colLetter;
                    } elseif (str_contains($h, 'pekerjaan ayah')) {
                        $colMap['father_occupation'] = $colLetter;
                    } elseif (str_contains($h, 'nama ibu')) {
                        $colMap['mother_name'] = $colLetter;
                    } elseif (str_contains($h, 'pekerjaan ibu')) {
                        $colMap['mother_occupation'] = $colLetter;
                    } elseif (str_contains($h, 'rombel') || str_contains($h, 'rombongan belajar') || str_contains($h, 'kelas')) {
                        $colMap['rombel'] = $colLetter;
                    }
                }

                // Default Dapodik daftar_pd fallbacks
                $colMap = array_merge([
                    'nama' => 'B',
                    'nis' => 'C',
                    'gender' => 'D',
                    'nisn' => 'E',
                    'birth_place' => 'F',
                    'birth_date' => 'G',
                    'nik' => 'H',
                    'religion' => 'I',
                    'address' => 'J',
                    'rt' => 'K',
                    'rw' => 'L',
                    'village' => 'N',
                    'district' => 'O',
                    'postal_code' => 'P',
                    'phone' => 'S',
                    'whatsapp' => 'T',
                    'email' => 'U',
                    'father_name' => 'Y',
                    'father_occupation' => 'AB',
                    'mother_name' => 'AE',
                    'mother_occupation' => 'AH',
                    'rombel' => 'AQ',
                ], $colMap);

                // Start row: skip column number row if present (e.g. Dapodik row 6)
                $startRow = $headerRow + 1;
                $testVal = trim((string)$sheet->getCell($colMap['nama'] . $startRow)->getValue());
                if (empty($testVal) || is_numeric($testVal)) {
                    $startRow++;
                }

                $existingNis = Student::pluck('nis')->flip()->toArray();
                $seenNis = [];

                for ($row = $startRow; $row <= $highestRow; $row++) {
                    $nama = trim((string)$sheet->getCell($colMap['nama'] . $row)->getValue());
                    $nis = trim((string)$sheet->getCell($colMap['nis'] . $row)->getValue());
                    $gender = strtoupper(trim((string)$sheet->getCell($colMap['gender'] . $row)->getValue()));
                    $nisn = trim((string)$sheet->getCell($colMap['nisn'] . $row)->getValue());
                    $rombel = trim((string)$sheet->getCell($colMap['rombel'] . $row)->getValue());

                    // Skip empty rows
                    if (empty($nama) && empty($nis)) {
                        continue;
                    }

                    // Check RPL / PPLG exclusivity
                    $upperRombel = strtoupper($rombel);
                    $isRplClass = empty($rombel) || str_contains($upperRombel, 'RPL') || str_contains($upperRombel, 'PPLG');

                    if (!$isRplClass) {
                        // Non-RPL students are automatically skipped without generating error cards
                        $skippedCount++;
                        continue;
                    }

                    $errors = [];
                    if (empty($nis) || !is_numeric($nis)) {
                        $errors[] = 'NIS (NIPD) wajib diisi dan harus berupa angka.';
                    } else {
                        if (isset($existingNis[$nis])) {
                            $errors[] = "NIS {$nis} sudah digunakan di database.";
                        }
                        if (isset($seenNis[$nis])) {
                            $errors[] = "NIS {$nis} ganda di dalam file ini.";
                        } else {
                            $seenNis[$nis] = true;
                        }
                    }

                    if (empty($nama)) {
                        $errors[] = 'Nama lengkap wajib diisi.';
                    }

                    if ($gender !== 'L' && $gender !== 'P') {
                        $errors[] = 'Jenis Kelamin harus L atau P.';
                    }

                    if (count($errors) > 0) {
                        $errorRows[] = [
                            'row' => $row,
                            'message' => implode('; ', $errors),
                            'data' => ['nis' => $nis, 'nama' => $nama, 'gender' => $gender, 'rombel' => $rombel]
                        ];
                    } else {
                        $validRows[] = [
                            'nis' => $nis,
                            'nisn' => $nisn,
                            'full_name' => $nama,
                            'gender' => $gender,
                            'birth_place' => $sheet->getCell($colMap['birth_place'] . $row)->getValue(),
                            'birth_date' => $this->parseExcelDate($sheet->getCell($colMap['birth_date'] . $row)->getValue()),
                            'nik' => $sheet->getCell($colMap['nik'] . $row)->getValue(),
                            'religion' => $sheet->getCell($colMap['religion'] . $row)->getValue() ?? 'Islam',
                            'address' => $sheet->getCell($colMap['address'] . $row)->getValue(),
                            'rt' => $sheet->getCell($colMap['rt'] . $row)->getValue(),
                            'rw' => $sheet->getCell($colMap['rw'] . $row)->getValue(),
                            'village' => $sheet->getCell($colMap['village'] . $row)->getValue(),
                            'district' => $sheet->getCell($colMap['district'] . $row)->getValue(),
                            'postal_code' => $sheet->getCell($colMap['postal_code'] . $row)->getValue(),
                            'phone' => $sheet->getCell($colMap['phone'] . $row)->getValue(),
                            'whatsapp' => $sheet->getCell($colMap['whatsapp'] . $row)->getValue(),
                            'email' => $sheet->getCell($colMap['email'] . $row)->getValue(),
                            
                            // Parents
                            'father_name' => $sheet->getCell($colMap['father_name'] . $row)->getValue(),
                            'father_occupation' => $sheet->getCell($colMap['father_occupation'] . $row)->getValue(),
                            'mother_name' => $sheet->getCell($colMap['mother_name'] . $row)->getValue(),
                            'mother_occupation' => $sheet->getCell($colMap['mother_occupation'] . $row)->getValue(),
                            
                            'class_name' => $rombel,
                            'entry_year' => date('Y'),
                        ];
                    }
                }
            } else {
                // Guru / Tendik starts from Row 6
                $startRow = 6;
                $ptkType = $importType === 'guru' ? 'Guru' : 'Tenaga Kependidikan';
                $existingNip = Employee::pluck('nip')->flip()->toArray();
                $seenNip = [];

                for ($row = $startRow; $row <= $highestRow; $row++) {
                    $nama = $sheet->getCell('B' . $row)->getValue();
                    $nip = $sheet->getCell('G' . $row)->getValue();
                    $gender = strtoupper(trim($sheet->getCell('D' . $row)->getValue() ?? ''));
                    
                    if (empty($nama) && empty($nip)) {
                        continue;
                    }

                    $errors = [];
                    if (empty($nip) || !is_numeric($nip)) {
                        $errors[] = 'NIP wajib diisi dan harus berupa angka.';
                    } else {
                        if (isset($existingNip[$nip])) {
                            $errors[] = "NIP {$nip} sudah digunakan di database.";
                        }
                        if (isset($seenNip[$nip])) {
                            $errors[] = "NIP {$nip} ganda di dalam file ini.";
                        } else {
                            $seenNip[$nip] = true;
                        }
                    }

                    if (empty($nama)) {
                        $errors[] = 'Nama lengkap wajib diisi.';
                    }

                    if ($gender !== 'L' && $gender !== 'P') {
                        $errors[] = 'Jenis Kelamin harus L atau P.';
                    }

                    if (count($errors) > 0) {
                        $errorRows[] = [
                            'row' => $row,
                            'message' => implode('; ', $errors),
                            'data' => ['nip' => $nip, 'nama' => $nama, 'gender' => $gender]
                        ];
                    } else {
                        $validRows[] = [
                            'full_name' => $nama,
                            'nip' => $nip,
                            'nuptk' => $sheet->getCell('C' . $row)->getValue(),
                            'gender' => $gender,
                            'birth_place' => $sheet->getCell('E' . $row)->getValue(),
                            'birth_date' => $this->parseExcelDate($sheet->getCell('F' . $row)->getValue()),
                            'status_kepegawaian' => $sheet->getCell('H' . $row)->getValue(),
                            'ptk_type' => $ptkType,
                            'religion' => $sheet->getCell('J' . $row)->getValue() ?? 'Islam',
                            'address' => $sheet->getCell('K' . $row)->getValue(),
                            'rt' => $sheet->getCell('L' . $row)->getValue(),
                            'rw' => $sheet->getCell('M' . $row)->getValue(),
                            'village' => $sheet->getCell('O' . $row)->getValue(),
                            'district' => $sheet->getCell('P' . $row)->getValue(),
                            'postal_code' => $sheet->getCell('Q' . $row)->getValue(),
                            'phone' => $sheet->getCell('R' . $row)->getValue(),
                            'whatsapp' => $sheet->getCell('R' . $row)->getValue(),
                            'email' => $sheet->getCell('S' . $row)->getValue(),
                        ];
                    }
                }
            }

            // Free spreadsheet memory before caching and rendering view
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            // Save in cache
            $cacheKey = 'import_temp_' . auth()->id();
            Cache::put($cacheKey, [
                'type' => $importType,
                'rows' => $validRows
            ], 7200); // 2 hours cache lifetime

            $totalDataRows = count($validRows) + count($errorRows) + $skippedCount;

            return view('admin.students.import-preview', [
                'importType' => $importType,
                'total' => $totalDataRows > 0 ? $totalDataRows : ($highestRow - ($importType === 'siswa' ? 6 : 5)),
                'validCount' => count($validRows),
                'errorCount' => count($errorRows),
                'skippedCount' => $skippedCount,
                'errorRows' => $errorRows,
                'validRows' => $validRows,
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    public function submitImport(Request $request)
    {
        $cacheKey = 'import_temp_' . auth()->id();
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData || !isset($cachedData['rows']) || count($cachedData['rows']) === 0) {
            return redirect()->route('admin.students.import-export-ui')->with('error', 'Data import kadaluarsa atau tidak ditemukan. Silakan upload ulang.');
        }

        $importType = $cachedData['type'];
        $validRows = $cachedData['rows'];

        $activeYear = SchoolYear::where('is_active', true)->first();
        $activeSemester = Semester::where('is_active', true)->first();

        DB::beginTransaction();
        try {
            $importedCount = 0;
            
            if ($importType === 'siswa') {
                foreach ($validRows as $row) {
                    $student = Student::create([
                        'uuid' => Str::uuid()->toString(),
                        'nis' => $row['nis'],
                        'nisn' => $row['nisn'],
                        'full_name' => $row['full_name'],
                        'gender' => $row['gender'],
                        'birth_place' => $row['birth_place'],
                        'birth_date' => $row['birth_date'],
                        'religion' => $row['religion'],
                        'entry_year' => $row['entry_year'],
                        'phone' => $row['phone'],
                        'whatsapp' => $row['whatsapp'],
                        'email' => $row['email'],
                        'address' => $row['address'],
                        'rt' => $row['rt'],
                        'rw' => $row['rw'],
                        'village' => $row['village'],
                        'district' => $row['district'],
                        'postal_code' => $row['postal_code'],
                        'status' => 'Aktif',
                        
                        'father_name' => $row['father_name'],
                        'father_occupation' => $row['father_occupation'],
                        'mother_name' => $row['mother_name'],
                        'mother_occupation' => $row['mother_occupation'],
                        
                        'nik' => $row['nik'] ?: null,
                    ]);

                    // Login Account (Password default: Tanggal Lahir DDMMYYYY)
                    $birthDate = $student->birth_date;
                    $defaultPassword = $birthDate ? date('dmY', strtotime($birthDate)) : 'Password123!';

                    User::create([
                        'name' => $student->full_name,
                        'email' => $student->nis . '@smkn1-cirebon.sch.id',
                        'username' => $student->nis,
                        'role' => 'siswa',
                        'student_id' => $student->id,
                        'password' => Hash::make($defaultPassword),
                    ]);

                    // Assign to Class (Get or create automatically based on Dapodik name)
                    if ($row['class_name'] && $activeYear && $activeSemester) {
                        $class = $this->getOrCreateClass($row['class_name'], $activeYear->id);
                        if ($class) {
                            ClassMember::create([
                                'student_id' => $student->id,
                                'class_id' => $class->id,
                                'school_year_id' => $activeYear->id,
                                'semester_id' => $activeSemester->id,
                                'status' => 'Active'
                            ]);
                        }
                    }
                    $importedCount++;
                }
            } else {
                foreach ($validRows as $row) {
                    $employee = Employee::create([
                        'uuid' => Str::uuid()->toString(),
                        'nip' => $row['nip'],
                        'nuptk' => $row['nuptk'],
                        'full_name' => $row['full_name'],
                        'gender' => $row['gender'],
                        'birth_place' => $row['birth_place'],
                        'birth_date' => $row['birth_date'],
                        'religion' => $row['religion'],
                        'status' => 'Aktif',
                        'ptk_type' => $row['ptk_type'],
                        'employment_type' => $row['status_kepegawaian'],
                        'address' => $row['address'],
                        'rt' => $row['rt'],
                        'rw' => $row['rw'],
                        'village' => $row['village'],
                        'district' => $row['district'],
                        'postal_code' => $row['postal_code'],
                        'phone' => $row['phone'],
                        'whatsapp' => $row['whatsapp'],
                        'email' => $row['email'],
                    ]);

                    // Login Account (Password default: Tanggal Lahir DDMMYYYY)
                    $birthDate = $employee->birth_date;
                    $defaultPassword = $birthDate ? date('dmY', strtotime($birthDate)) : 'Password123!';

                    User::create([
                        'name' => $employee->full_name,
                        'email' => $employee->nip . '@smkn1-cirebon.sch.id',
                        'username' => $employee->nip,
                        'role' => 'employee',
                        'employee_id' => $employee->id,
                        'password' => Hash::make($defaultPassword),
                    ]);

                    $importedCount++;
                }
            }

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Import ' . ucfirst($importType),
                'description' => "Berhasil mengimpor {$importedCount} data {$importType} melalui file Excel.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Cache::forget($cacheKey);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.students.import-export-ui')->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.index')->with('success', "Berhasil mengimpor {$importedCount} data {$importType}.");
    }

    public function export(Request $request)
    {
        $classFilter = $request->input('class_id');
        $yearFilter = $request->input('entry_year');
        $genderFilter = $request->input('gender');
        $statusFilter = $request->input('status');

        $query = Student::query();
        $query->leftJoin('class_members', function ($join) {
            $join->on('students.id', '=', 'class_members.student_id')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('school_years')
                      ->whereColumn('school_years.id', 'class_members.school_year_id')
                      ->where('school_years.is_active', true);
                })
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('semesters')
                      ->whereColumn('semesters.id', 'class_members.semester_id')
                      ->where('semesters.is_active', true);
                });
        })
        ->leftJoin('classes', 'class_members.class_id', '=', 'classes.id')
        ->select('students.*', 'classes.name as class_name');

        if ($classFilter) {
            $query->where('class_members.class_id', $classFilter);
        }
        if ($yearFilter) {
            $query->where('students.entry_year', $yearFilter);
        }
        if ($genderFilter) {
            $query->where('students.gender', $genderFilter);
        }
        if ($statusFilter) {
            $query->where('students.status', $statusFilter);
        }

        $students = $query->get();

        $filename = "siswa_export_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = ['NIS', 'NISN', 'Nama Lengkap', 'Jenis Kelamin', 'Kelas', 'Tahun Masuk', 'Status', 'No HP', 'Email', 'Alamat'];

        $callback = function () use ($students, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->nis,
                    $student->nisn,
                    $student->full_name,
                    $student->gender,
                    $student->class_name ?? '-',
                    $student->entry_year,
                    $student->status,
                    $student->phone,
                    $student->email,
                    $student->address,
                ]);
            }
            fclose($file);
        };

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'Export Students',
            'description' => "Mengekspor data siswa ke file CSV (Total: " . $students->count() . " siswa).",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->stream($callback, 200, $headers);
    }

    public function submitImportChunk(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $request->validate([
            'chunk_index' => 'required|integer|min:0',
            'chunk_size' => 'required|integer|min:1',
        ]);

        $cacheKey = 'import_temp_' . auth()->id();
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData || !isset($cachedData['rows']) || count($cachedData['rows']) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data import kadaluarsa atau tidak ditemukan. Silakan upload ulang.'
            ], 400);
        }

        $importType = $cachedData['type'];
        $validRows = $cachedData['rows'];
        $chunkIndex = intval($request->input('chunk_index'));
        $chunkSize = intval($request->input('chunk_size'));

        $slice = array_slice($validRows, $chunkIndex * $chunkSize, $chunkSize);
        if (count($slice) === 0) {
            return response()->json([
                'success' => true,
                'processed' => 0,
                'total_processed' => count($validRows),
                'total_rows' => count($validRows),
                'is_finished' => true
            ]);
        }

        $activeYear = SchoolYear::where('is_active', true)->first();
        $activeSemester = Semester::where('is_active', true)->first();

        DB::beginTransaction();
        try {
            $processedCount = 0;
            if ($importType === 'siswa') {
                foreach ($slice as $row) {
                    $student = Student::firstOrNew(['nis' => $row['nis']]);
                    if (!$student->exists) {
                        $student->uuid = Str::uuid()->toString();
                    }
                    $student->nisn = $row['nisn'] ?: null;
                    $student->full_name = $row['full_name'];
                    $student->gender = $row['gender'];
                    $student->birth_place = $row['birth_place'];
                    $student->birth_date = $row['birth_date'];
                    $student->religion = $row['religion'];
                    $student->entry_year = $row['entry_year'];
                    $student->phone = $row['phone'];
                    $student->whatsapp = $row['whatsapp'];
                    $student->email = $row['email'];
                    $student->address = $row['address'];
                    $student->rt = $row['rt'];
                    $student->rw = $row['rw'];
                    $student->village = $row['village'];
                    $student->district = $row['district'];
                    $student->postal_code = $row['postal_code'];
                    $student->status = 'Aktif';
                    $student->father_name = $row['father_name'];
                    $student->father_occupation = $row['father_occupation'];
                    $student->mother_name = $row['mother_name'];
                    $student->mother_occupation = $row['mother_occupation'];
                    $student->nik = $row['nik'] ?: null;
                    $student->save();

                    // Login Account (Password default: Tanggal Lahir DDMMYYYY)
                    $birthDate = $student->birth_date;
                    $defaultPassword = $birthDate ? date('dmY', strtotime($birthDate)) : 'Password123!';
                    $studentEmail = $student->nis . '@smkn1-cirebon.sch.id';

                    $user = User::where('student_id', $student->id)
                        ->orWhere('username', $student->nis)
                        ->orWhere('email', $studentEmail)
                        ->first();

                    if (!$user) {
                        User::create([
                            'name' => $student->full_name,
                            'email' => $studentEmail,
                            'username' => $student->nis,
                            'role' => 'siswa',
                            'student_id' => $student->id,
                            'password' => $defaultPassword,
                        ]);
                    }

                    // Assign to Class (Get or create automatically based on Dapodik name)
                    if ($row['class_name'] && $activeYear && $activeSemester) {
                        $class = $this->getOrCreateClass($row['class_name'], $activeYear->id);
                        if ($class) {
                            ClassMember::firstOrCreate([
                                'student_id' => $student->id,
                                'school_year_id' => $activeYear->id,
                                'semester_id' => $activeSemester->id,
                            ], [
                                'class_id' => $class->id,
                                'status' => 'Active'
                            ]);
                        }
                    }
                    $processedCount++;
                }
            } else {
                foreach ($slice as $row) {
                    $employee = Employee::firstOrNew(['nip' => $row['nip']]);
                    if (!$employee->exists) {
                        $employee->uuid = Str::uuid()->toString();
                    }
                    $employee->nuptk = $row['nuptk'];
                    $employee->full_name = $row['full_name'];
                    $employee->gender = $row['gender'];
                    $employee->birth_place = $row['birth_place'];
                    $employee->birth_date = $row['birth_date'];
                    $employee->religion = $row['religion'];
                    $employee->status = 'Aktif';
                    $employee->ptk_type = $row['ptk_type'];
                    $employee->employment_type = $row['status_kepegawaian'];
                    $employee->address = $row['address'];
                    $employee->rt = $row['rt'];
                    $employee->rw = $row['rw'];
                    $employee->village = $row['village'];
                    $employee->district = $row['district'];
                    $employee->postal_code = $row['postal_code'];
                    $employee->phone = $row['phone'];
                    $employee->whatsapp = $row['whatsapp'];
                    $employee->email = $row['email'];
                    $employee->save();

                    // Login Account (Password default: Tanggal Lahir DDMMYYYY)
                    $birthDate = $employee->birth_date;
                    $defaultPassword = $birthDate ? date('dmY', strtotime($birthDate)) : 'Password123!';
                    $employeeEmail = $employee->nip . '@smkn1-cirebon.sch.id';

                    $user = User::where('employee_id', $employee->id)
                        ->orWhere('username', $employee->nip)
                        ->orWhere('email', $employeeEmail)
                        ->first();

                    if (!$user) {
                        User::create([
                            'name' => $employee->full_name,
                            'email' => $employeeEmail,
                            'username' => $employee->nip,
                            'role' => 'employee',
                            'employee_id' => $employee->id,
                            'password' => $defaultPassword,
                        ]);
                    }
                    $processedCount++;
                }
            }

            $totalProcessed = ($chunkIndex * $chunkSize) + $processedCount;
            $isFinished = $totalProcessed >= count($validRows);

            if ($isFinished) {
                // Audit Log
                \App\Models\AuditLog::create([
                    'user_id' => auth()->id(),
                    'event' => 'Import ' . ucfirst($importType),
                    'description' => "Berhasil mengimpor keseluruhan " . count($validRows) . " data {$importType} melalui file Excel.",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                Cache::forget($cacheKey);
            } else {
                // Renew cache lifetime to prevent expiry mid-import
                Cache::put($cacheKey, $cachedData, 7200);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'processed' => $processedCount,
                'total_processed' => $totalProcessed,
                'total_rows' => count($validRows),
                'is_finished' => $isFinished
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses batch import: ' . $e->getMessage()
            ], 500);
        }
    }

    private function parseExcelDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        if (is_numeric($dateValue)) {
            // Excel integer date
            try {
                return date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($dateValue));
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            return date('Y-m-d', strtotime($dateValue));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getOrCreateClass($className, $schoolYearId)
    {
        if (empty($className)) {
            return null;
        }

        $className = trim($className);

        // Find existing class by name in the active year
        $class = SchoolClass::where('name', $className)
            ->where('school_year_id', $schoolYearId)
            ->first();

        if ($class) {
            return $class;
        }

        // Determine grade_level from Roman numeral prefix
        $gradeLevel = 10;
        $upperName = strtoupper($className);
        if (str_starts_with($upperName, 'XII')) {
            $gradeLevel = 12;
        } elseif (str_starts_with($upperName, 'XI')) {
            $gradeLevel = 11;
        } elseif (str_starts_with($upperName, 'X')) {
            $gradeLevel = 10;
        }

        // Determine major code and name from class name keywords
        $majorCode = null;
        $majorName = null;

        $majorMap = [
            'PPLG'  => ['code' => 'PPLG',  'name' => 'Pengembangan Perangkat Lunak dan Gim'],
            'RPL'   => ['code' => 'RPL',   'name' => 'Rekayasa Perangkat Lunak'],
        ];

        foreach ($majorMap as $keyword => $data) {
            if (str_contains($upperName, $keyword)) {
                $majorCode = $data['code'];
                $majorName = $data['name'];
                break;
            }
        }

        // If no keyword matched, default to PPLG (WargaRPL)
        if (!$majorCode) {
            $majorCode = 'PPLG';
            $majorName = 'Pengembangan Perangkat Lunak dan Gim';
        }

        // Find or create the major dynamically (no hardcoded IDs)
        $major = \App\Models\Major::firstOrCreate(
            ['code' => $majorCode],
            ['name' => $majorName]
        );

        // Create new class on-the-fly
        return SchoolClass::create([
            'name'           => $className,
            'school_year_id' => $schoolYearId,
            'major_id'       => $major->id,
            'grade_level'    => $gradeLevel,
            'status'         => true
        ]);
    }
}
