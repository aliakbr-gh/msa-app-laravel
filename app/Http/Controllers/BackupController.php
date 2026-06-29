<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
    public function download()
    {
        $backupDirectory = storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);

        $fileName = 'restaurant-erp-backup-'.now('Asia/Karachi')->format('d-m-Y-h-i-A').'.zip';
        $zipPath = $backupDirectory.'/'.$fileName;

        $zip = new ZipArchive();
        abort_unless($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true, 500, 'Unable to create backup file.');

        $this->addDatabaseBackup($zip);
        $this->addDirectory($zip, storage_path('app/public'), 'storage-app-public');
        $this->addDirectory($zip, public_path('images'), 'public-images');

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function addDatabaseBackup(ZipArchive $zip): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");

        if ($driver === 'sqlite') {
            $configuredPath = config("database.connections.$connection.database");
            $sqlitePath = str_starts_with($configuredPath, DIRECTORY_SEPARATOR)
                ? $configuredPath
                : database_path($configuredPath);

            if (File::isFile($sqlitePath)) {
                $zip->addFile($sqlitePath, 'database/database.sqlite');
            }

            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'])) {
            $zip->addFromString('database/database.sql', $this->mysqlDump());

            return;
        }

        $zip->addFromString('database/database.json', json_encode($this->jsonDump(), JSON_PRETTY_PRINT));
    }

    private function mysqlDump(): string
    {
        $tables = collect(DB::select('SHOW TABLES'))
            ->map(fn ($table) => array_values((array) $table)[0])
            ->values();

        $dump = "-- Restaurant ERP backup generated at ".now()->toDateTimeString()."\n\n";
        $dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $create = DB::select("SHOW CREATE TABLE `$table`")[0]->{'Create Table'};
            $dump .= "DROP TABLE IF EXISTS `$table`;\n";
            $dump .= $create.";\n\n";

            foreach (DB::table($table)->cursor() as $row) {
                $values = collect((array) $row)
                    ->map(fn ($value) => $value === null ? 'NULL' : DB::getPdo()->quote((string) $value))
                    ->implode(', ');

                $columns = collect(array_keys((array) $row))
                    ->map(fn ($column) => "`$column`")
                    ->implode(', ');

                $dump .= "INSERT INTO `$table` ($columns) VALUES ($values);\n";
            }

            $dump .= "\n";
        }

        return $dump."SET FOREIGN_KEY_CHECKS=1;\n";
    }

    private function jsonDump(): array
    {
        return collect(DB::select('select table_name from information_schema.tables where table_schema = current_schema()'))
            ->pluck('table_name')
            ->mapWithKeys(fn ($table) => [$table => DB::table($table)->get()])
            ->all();
    }

    private function addDirectory(ZipArchive $zip, string $path, string $zipPrefix): void
    {
        if (! File::isDirectory($path)) {
            return;
        }

        foreach (File::allFiles($path) as $file) {
            $relativePath = $zipPrefix.'/'.str_replace('\\', '/', $file->getRelativePathname());
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }
}
