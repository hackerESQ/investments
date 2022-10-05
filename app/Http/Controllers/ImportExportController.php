<?php

namespace App\Http\Controllers;

use App\Exports\BackupExport;
use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\TransactionExport;
use App\Imports\TransactionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\TransactionRequest;
use App\Imports\BackupImport;

class ImportExportController extends Controller
{
       /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $file = $request->file('import')->store('/', 'local');

        $import = (new BackupImport)->import($file, 'local', \Maatwebsite\Excel\Excel::XLSX);

        return redirect(route('portfolio.index'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        return Excel::download(new BackupExport, now()->format('Y_m_d') . '_investments_backup.xlsx');
    }
}
