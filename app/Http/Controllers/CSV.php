<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Response;
class CSV extends Controller
{
    public function make_csv($company_id,$year){

        $yearlyData = [
            'January'   => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
                // Add more data as needed for January
            ],
            'February'  => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'March'     => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'April'     => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'May'       => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'June'      => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'July'      => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'August'    => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'September' => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'October'   => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'November'  => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
            'December'  => [
                ['John Doe', 'john@example.com', '555-1234'],
                ['Jane Doe', 'jane@example.com', '555-5678'],
            ],
        ];

        // $headers = array(
        //     "Content-type"        => "text/csv",
        //     "Pragma"              => "no-cache",
        //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires"             => "0"
        // );

        // // Loop through each month
        // foreach ($yearlyData as $month => $data) {
        //     // Create the CSV file
        //     $filename = strtolower($month) . '_data.csv';
        //     $handle = fopen('php://output', 'w');

        //     // Add header row
        //     fputcsv($handle, ['Name', 'Email', 'Phone']);

        //     // Add data rows
        //     foreach ($data as $row) {
        //         fputcsv($handle, $row);
        //     }

        //     fclose($handle);

        //     // Download the CSV file for each month
        //     $headers["Content-Disposition"] = "attachment; filename=$filename";
        //     Response::make('', 200, $headers)->send();
        // }
        // $headers = array(
        //     "Content-type"        => "text/csv",
        //     "Pragma"              => "no-cache",
        //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires"             => "0"
        // );

        // // Create the CSV file
        // $filename = 'yearly_data.csv';
        // $handle = fopen('php://output', 'w');

        // // Loop through each set of three months
        // foreach (array_chunk($yearlyData, 3, true) as $monthSet) {
        //     // Add header row for the set
        //     $headerRow = [];
        //     foreach ($monthSet as $month => $data) {
        //         $headerRow[] = $month;
        //     }
        //     fputcsv($handle, $headerRow);

        //     // Find the maximum count of rows for any month in the set
        //     $maxRows = max(array_map('count', $monthSet));

        //     // Add data rows
        //     for ($i = 0; $i < $maxRows; $i++) {
        //         $rowData = [];
        //         foreach ($monthSet as $month => $data) {
        //             $rowData = array_merge($rowData, $data[$i] ?? array_fill(0, count(reset($data)), ''));
        //         }
        //         fputcsv($handle, $rowData);
        //     }
        // }

        // fclose($handle);

        // // Download the CSV file
        // $headers["Content-Disposition"] = "attachment; filename=$filename";
        // Response::make('', 200, $headers)->send();


        // $headers = array(
        //     "Content-type"        => "text/csv",
        //     "Pragma"              => "no-cache",
        //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires"             => "0"
        // );

        // // Create the CSV file
        // $filename = 'yearly_data.csv';
        // $handle = fopen('php://output', 'w');

        // // Loop through each month
        // foreach ($yearlyData as $month => $data) {
        //     // Add header row for the month
        //     fputcsv($handle, [$month]);

        //     // Add blank row
        //     fputcsv($handle, []);

        //     // Add data rows
        //     foreach ($data as $row) {
        //         fputcsv($handle, $row);
        //     }

        //     // Add blank row between months
        //     fputcsv($handle, []);
        // }

        // fclose($handle);

        // // Download the CSV file
        // $headers["Content-Disposition"] = "attachment; filename=$filename";
        // Response::make('', 200, $headers)->send();

        // $headers = array(
        //     "Content-type"        => "text/csv",
        //     "Pragma"              => "no-cache",
        //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires"             => "0"
        // );

        // // Create the CSV file
        // $filename = 'yearly_data.csv';
        // $handle = fopen('php://output', 'w');

        // // Add blank rows and columns for spacing
        // for ($i = 0; $i < 2; $i++) {
        //     fputcsv($handle, []);
        // }

        // // Loop through each month
        // foreach ($yearlyData as $month => $data) {
        //     // Add blank columns for spacing
        //     fputcsv($handle, ['', '']);

        //     // Add header row for the month
        //     fputcsv($handle, [$month]);

        //     // Add data rows
        //     foreach ($data as $row) {
        //         fputcsv($handle, $row);
        //     }

        //     // Add blank rows and columns for spacing
        //     for ($i = 0; $i < 2; $i++) {
        //         fputcsv($handle, ['', '']);
        //     }
        // }

        // fclose($handle);

        // // Download the CSV file
        // $headers["Content-Disposition"] = "attachment; filename=$filename";
        // Response::make('', 200, $headers)->send();
        // $headers = array(
        //     "Content-type"        => "text/csv",
        //     "Pragma"              => "no-cache",
        //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires"             => "0"
        // );

        // // Create the CSV file
        // $filename = 'yearly_data.csv';
        // $handle = fopen('php://output', 'w');

        // // Loop through each set of three months
        // foreach (array_chunk($yearlyData, 3, true) as $monthSet) {
        //     // Add blank columns for spacing
        //     fputcsv($handle, ['', '']);

        //     // Add header rows for the set
        //     foreach ($monthSet as $month => $data) {
        //         fputcsv($handle, [$month]);
        //     }

        //     // Find the maximum count of rows for any month in the set
        //     $maxRows = max(array_map('count', $monthSet));

        //     // Add data rows
        //     for ($i = 0; $i < $maxRows; $i++) {
        //         $rowData = [];
        //         foreach ($monthSet as $month => $data) {
        //             $rowData = array_merge($rowData, $data[$i] ?? array_fill(0, count(reset($data)), ''));
        //         }
        //         fputcsv($handle, $rowData);
        //     }
        // }

        // fclose($handle);

        // // Download the CSV file
        // $headers["Content-Disposition"] = "attachment; filename=$filename";
        // Response::make('', 200, $headers)->send();
        // $headers = array(
        //     "Content-type"        => "text/csv",
        //     "Pragma"              => "no-cache",
        //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires"             => "0"
        // );

        // // Create the CSV file
        // $filename = 'yearly_data.csv';
        // $handle = fopen('php://output', 'w');

        // // Loop through each set of three months
        // foreach (array_chunk($yearlyData, 3, true) as $monthSet) {
        //     // Add blank columns for spacing
        //     fputcsv($handle, ['', '', '']);

        //     // Find the maximum count of rows for any month in the set
        //     $maxRows = max(array_map('count', $monthSet));

        //     // Add header rows for the set
        //     for ($i = 0; $i < $maxRows; $i++) {
        //         foreach ($monthSet as $month => $data) {
        //             $monthName = ($i === 0) ? $month : '';
        //             fputcsv($handle, [$monthName]);
        //         }
        //     }

        //     // Add data rows
        //     for ($i = 0; $i < $maxRows; $i++) {
        //         $rowData = [];
        //         foreach ($monthSet as $month => $data) {
        //             $rowData = array_merge($rowData, $data[$i] ?? array_fill(0, count(reset($data)), ''));
        //         }
        //         fputcsv($handle, $rowData);
        //     }
        // }

        // fclose($handle);

        // // Download the CSV file
        // $headers["Content-Disposition"] = "attachment; filename=$filename";
        // Response::make('', 200, $headers)->send();

        // $headers = array(
        //     "Content-type"        => "text/csv",
        //     "Pragma"              => "no-cache",
        //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires"             => "0"
        // );

        // // Create the CSV file
        // $filename = 'yearly_data.csv';
        // $handle = fopen('php://output', 'w');

        // // Loop through each month
        // foreach ($yearlyData as $month => $data) {
        //     // Add blank columns for spacing
        //     fputcsv($handle, ['', '']);

        //     // Add header row for the month
        //     fputcsv($handle, [$month]);

        //     // Add data rows
        //     foreach ($data as $row) {
        //         fputcsv($handle, $row);
        //     }
        // }

        // fclose($handle);

        // // Download the CSV file
        // $headers["Content-Disposition"] = "attachment; filename=$filename";
        // Response::make('', 200, $headers)->send();

        // $headers = array(
        //     "Content-type"        => "text/csv",
        //     "Pragma"              => "no-cache",
        //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //     "Expires"             => "0"
        // );

        // // Create the CSV file
        // $filename = 'yearly_data.csv';
        // $handle = fopen('php://output', 'w');

        // // Add header row
        // fputcsv($handle, ['Month', 'Name', 'Email', 'Phone']);

        // // Loop through each set of three months
        // foreach (array_chunk($yearlyData, 3, true) as $monthSet) {
        //     // Add data rows
        //     foreach ($monthSet as $month => $data) {
        //         foreach ($data as $row) {
        //             // Add the month name as the first column
        //             fputcsv($handle, array_merge([$month], $row));
        //         }
        //     }
        // }

        // fclose($handle);

        // // Download the CSV file
        // $headers["Content-Disposition"] = "attachment; filename=$filename";
        // Response::make('', 200, $headers)->send();

        $headers = array(
            "Content-type"        => "text/csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        // Create the CSV file
        $filename = 'yearly_data.csv';
        $handle = fopen('php://output', 'w');

        // Add header row
        fputcsv($handle, ['Month', 'Name', 'Email', 'Phone']);

        // Define the column sets
        $columnSets = [
            ['A', 'B', 'C', 'D', 'E', 'F'],
            ['G', 'H', 'I', 'J', 'K'],
            ['M', 'N', 'O', 'P', 'Q'],
        ];

        // Loop through each set of three months
        foreach (array_chunk($yearlyData, 3, true) as $monthSet) {
            foreach ($columnSets as $columns) {
                // Add data rows
                foreach ($monthSet as $month => $data) {
                    foreach ($data as $row) {
                        // Add the month name as the first column
                        $rowData = [$month];
                        // Add data for the specified columns
                        foreach ($columns as $column) {
                            $rowData[] = $row[$column] ?? '';
                        }
                        fputcsv($handle, $rowData);
                    }
                }
            }
        }

        fclose($handle);

        // Download the CSV file
        $headers["Content-Disposition"] = "attachment; filename=$filename";
        Response::make('', 200, $headers)->send();
    
    }
}
