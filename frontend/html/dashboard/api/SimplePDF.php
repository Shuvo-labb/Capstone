<?php

class SimplePDF {
    private $pages = [];
    private $currentPageContent = "";
    private $y = 800;
    private $x = 50;
    
    public function AddPage() {
        if ($this->currentPageContent !== "") {
            $this->pages[] = $this->currentPageContent;
        }
        $this->currentPageContent = "BT /F1 10 Tf 14 TL ET\n";
        $this->y = 800;
        $this->x = 50;
    }
    
    public function SetFont($name, $size) {
        $fontName = ($name === 'Bold') ? '/F2' : '/F1';
        $this->currentPageContent .= "BT " . $fontName . " " . (float)$size . " Tf ET\n";
    }
    
    public function SetXY($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }
    
    public function Cell($w, $h, $txt, $border = 0, $ln = 0) {
        if ($this->y - $h < 50) {
            $this->AddPage();
            $this->SetFont('Normal', 10);
        }

        if ($border) {
            $x1 = $this->x;
            $y1 = $this->y;
            $x2 = $this->x + $w;
            $y2 = $this->y - $h;
            $this->currentPageContent .= sprintf("%.2f %.2f m %.2f %.2f l %.2f %.2f l %.2f %.2f l s\n", 
                $x1, $y1, $x2, $y1, $x2, $y2, $x1, $y2);
        }
        
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $txt);
        $textY = $this->y - ($h * 0.7);
        $textX = $this->x + 4;
        $this->currentPageContent .= sprintf("BT %.2f %.2f Td (%s) Tj ET\n", $textX, $textY, $escaped);
        
        if ($ln) {
            $this->x = 50;
            $this->y -= $h;
        } else {
            $this->x += $w;
        }
    }

    public function Ln($h = 15) {
        $this->x = 50;
        $this->y -= $h;
    }
    
    public function Output() {
        if ($this->currentPageContent !== "") {
            $this->pages[] = $this->currentPageContent;
            $this->currentPageContent = "";
        }
        
        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        
        $kids = [];
        $pageStartId = 5;
        for ($i = 0; $i < count($this->pages); $i++) {
            $kids[] = ($pageStartId + 2 * $i) . " 0 R";
        }
        $objects[2] = "<< /Type /Pages /Kids [" . implode(" ", $kids) . "] /Count " . count($this->pages) . " >>";
        $objects[3] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /MacRomanEncoding >>";
        $objects[4] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /MacRomanEncoding >>";
        
        for ($i = 0; $i < count($this->pages); $i++) {
            $pageId = $pageStartId + 2 * $i;
            $contentId = $pageId + 1;
            
            $objects[$pageId] = "<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /MediaBox [0 0 595 842] /Contents " . $contentId . " 0 R >>";
            
            $stream = $this->pages[$i];
            $objects[$contentId] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
        }
        
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $id => $obj) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $obj . "\nendobj\n";
        }
        
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= "0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($id = 1; $id <= count($objects); $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }
        
        $pdf .= "trailer\n";
        $pdf .= "<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF\n";
        
        return $pdf;
    }
}
?>
