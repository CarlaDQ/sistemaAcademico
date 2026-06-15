<?php

namespace App\Libraries;

class ReportPdf extends \FPDF
{
    protected $schoolName;
    protected $reportTitle;
    protected $isSchedule;

    public function __construct($title = 'Reporte Academico', $isSchedule = false)
    {
        parent::__construct('L', 'mm', 'A4');
        $this->schoolName = 'U.E. DAVID PINILLA';
        $this->reportTitle = $title;
        $this->isSchedule = $isSchedule;
        $this->AliasNbPages();
    }

    protected function e($text)
    {
        $text = str_replace(
            ["\u{2014}", "\u{2013}", "\u{2022}", "\u{201C}", "\u{201D}", "\u{2018}", "\u{2019}"],
            ['-', '-', '*', '"', '"', "'", "'"],
            $text
        );
        return mb_convert_encoding((string)$text, 'ISO-8859-1', 'UTF-8');
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        parent::Cell($w, $h, $this->e($txt), $border, $ln, $align, $fill, $link);
    }

    public function MultiCell($w, $h = 0, $txt = '', $border = 0, $align = '', $fill = false)
    {
        parent::MultiCell($w, $h, $this->e($txt), $border, $align, $fill);
    }

    public function Write($h, $txt, $link = '')
    {
        parent::Write($h, $this->e($txt), $link);
    }

    public function Header()
    {
        $w = $this->GetPageWidth() - 20;
        $logoPath = __DIR__ . '/../../public/img/logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 11, 14);
        }

        $this->SetDrawColor(30, 60, 114);
        $this->SetLineWidth(0.3);
        $this->Line(10, 10, 10 + $w, 10);

        $this->SetY(12);
        $this->SetX(27);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(30, 60, 114);
        $this->Cell(0, 9, $this->schoolName, 0, 1, 'C');

        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(130, 130, 130);
        $this->Cell(0, 5, $this->reportTitle . '  |  ' . date('d/m/Y H:i'), 0, 1, 'C');

        $this->SetDrawColor(30, 60, 114);
        $this->SetLineWidth(0.5);
        $this->Line(60, $this->GetY() + 1, 10 + $w - 50, $this->GetY() + 1);
        $this->Ln(6);
    }

    public function Footer()
    {
        $this->SetY(-14);
        $this->SetFont('Arial', 'I', 7.5);
        $this->SetTextColor(160, 160, 160);
        $this->Cell(0, 8, chr(169) . ' ' . date('Y') . ' U.E. David Pinilla - Pag. ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    public function ColoredTableHeader($headers, $widths)
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(30, 60, 114);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(30, 60, 114);

        foreach ($headers as $i => $h) {
            $this->Cell($widths[$i] ?? 30, 7, $h, 1, 0, 'C', true);
        }
        $this->Ln();

        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(50, 50, 50);
    }

    public function InfoBlock($data)
    {
        $startX = $this->GetX();
        $startY = $this->GetY();
        $rows = count($data);
        $blockH = ($rows * 6) + 4;

        $this->SetFillColor(245, 247, 250);
        $this->SetDrawColor(200, 210, 220);
        $this->Rect($startX, $startY, 277, $blockH, 'DF');

        $this->Ln(2);
        foreach ($data as $label => $value) {
            $this->SetX($startX + 4);
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(30, 60, 114);
            $this->Cell(28, 5.5, $label . ':', 0, 0);
            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(50, 50, 50);
            $this->Cell(0, 5.5, $value, 0, 1);
        }
        $this->Ln(3);
    }

    public function DrawScheduleCell($x, $y, $w, $h, $lines, $dayColor, $fill = false)
    {
        if ($fill) {
            $this->SetFillColor(248, 249, 252);
        } else {
            $this->SetFillColor(255, 255, 255);
        }
        $this->SetDrawColor(200, 210, 220);
        $this->Rect($x, $y, $w, $h, 'DF');

        if (empty($lines)) {
            $this->SetXY($x + $w, $y);
            return;
        }

        $lineH = 4;
        $yPos = $y + 1.5;

        foreach ($lines as $li => $line) {
            if ($yPos + $lineH > $y + $h - 1) break;
            $this->SetXY($x + 1, $yPos);

            if ($li === 0) {
                $this->SetFont('Arial', 'B', 7);
                $this->SetTextColor($dayColor[0], $dayColor[1], $dayColor[2]);
            } elseif ($li === 1) {
                $this->SetFont('Arial', '', 6);
                $this->SetTextColor(80, 80, 80);
            } else {
                $this->SetFont('Arial', '', 6);
                $this->SetTextColor(160, 160, 160);
            }

            $this->Cell($w - 2, $lineH, $line, 0, 1, 'C');
            $yPos += $lineH;
        }

        $this->SetXY($x + $w, $y);
    }
}
