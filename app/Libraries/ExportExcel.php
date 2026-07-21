<?php
namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class ExportExcel
{
    protected $spreadsheet;
    protected $sheet;
    protected $title;
    protected $subject;
    protected $description;
    protected $keywords;
    protected $author;
    protected $company;
    protected $headers = [];
    protected $data = [];
    
    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->author = 'CDW Accounting System';
    }
    
    /**
     * Set document properties
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->spreadsheet->getProperties()->setTitle($title);
        return $this;
    }
    
    public function setSubject($subject)
    {
        $this->subject = $subject;
        $this->spreadsheet->getProperties()->setSubject($subject);
        return $this;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        $this->spreadsheet->getProperties()->setDescription($description);
        return $this;
    }
    
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
        $this->spreadsheet->getProperties()->setKeywords($keywords);
        return $this;
    }
    
    public function setAuthor($author)
    {
        $this->author = $author;
        $this->spreadsheet->getProperties()->setCreator($author)
            ->setLastModifiedBy($author);
        return $this;
    }
    
    public function setCompany($company)
    {
        $this->company = $company;
        $this->spreadsheet->getProperties()->setCompany($company);
        return $this;
    }
    
    /**
     * Set headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        
        // Write headers
        $column = 'A';
        foreach ($headers as $header) {
            $this->sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        // Style headers
        $lastColumn = chr(ord('A') + count($headers) - 1);
        $headerRange = 'A1:' . $lastColumn . '1';
        
        $this->sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2c3e50']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
        
        // Set column width
        $this->sheet->getRowDimension(1)->setRowHeight(25);
        
        return $this;
    }
    
    /**
     * Set data
     */
    public function setData(array $data)
    {
        $this->data = $data;
        
        $row = 2;
        foreach ($data as $rowData) {
            $column = 'A';
            foreach ($rowData as $cellData) {
                $this->sheet->setCellValue($column . $row, $cellData);
                $column++;
            }
            $row++;
        }
        
        // Style data rows
        if (!empty($data)) {
            $lastColumn = chr(ord('A') + count($this->headers) - 1);
            $lastRow = count($data) + 1;
            $dataRange = 'A2:' . $lastColumn . $lastRow;
            
            $this->sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            
            // Alternate row colors
            for ($i = 2; $i <= $lastRow; $i++) {
                if ($i % 2 == 0) {
                    $rowRange = 'A' . $i . ':' . $lastColumn . $i;
                    $this->sheet->getStyle($rowRange)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF8F9FA');
                }
            }
            
            // Auto size columns
            foreach (range('A', $lastColumn) as $column) {
                $this->sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
        
        return $this;
    }
    
    /**
     * Set column widths
     */
    public function setColumnWidths(array $widths)
    {
        foreach ($widths as $column => $width) {
            $this->sheet->getColumnDimension($column)->setWidth($width);
        }
        return $this;
    }
    
    /**
     * Set freeze pane
     */
    public function setFreezePane($cell)
    {
        $this->sheet->freezePane($cell);
        return $this;
    }
    
    /**
     * Set auto filter
     */
    public function setAutoFilter($show = true)
    {
        if ($show && !empty($this->headers)) {
            $lastColumn = chr(ord('A') + count($this->headers) - 1);
            $this->sheet->setAutoFilter('A1:' . $lastColumn . '1');
        }
        return $this;
    }
    
    /**
     * Export to browser
     */
    public function export($filename = 'export.xlsx')
    {
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Save to file
     */
    public function save($filename)
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($filename);
        return $this;
    }
}

?>