<?php
if ($_SERVER["REQUEST_METHOD"] === "GET")
{
    header("HTTP/1.1 301 Moved Permanently"); 
    header("Location: /pdf/");
}

require_once(__DIR__ . "/vendor/autoload.php");
$params = (object)$_POST["param"];

$sagtarafyatay = $params->kart_boslugu + $params->kart_genisligi;

$pdf = new TCPDF('L', 'mm', 'A4');
if ($params->sayfa_boyu == "ozel") {
    if ($params->sayfa_ayrı=="ayni") {
        $ozel_boy = array(($params->ust_marj * 2) + $params->kart_yuksekligi, ($params->sol_marj * 2) + ($params->kart_genisligi * 2) + $params->kart_boslugu);
        $pdf = new TCPDF(L, 'mm', $ozel_boy);
    } else {
        $ozel_boy = array(($params->ust_marj * 2) + $params->kart_yuksekligi, ($params->sol_marj*2) + ($params->kart_genisligi));
        $pdf = new TCPDF(L, 'mm', $ozel_boy);
    }
} elseif ($params->sayfa_boyu == "A5"){
    $pdf = new TCPDF(L, 'mm', (string)$params->sayfa_boyu);
} else {
    $pdf = new TCPDF($params->sayfa_yonu, 'mm', (string)$params->sayfa_boyu);
}

//$pdf = new TCPDF($params->sayfa_yonu, 'mm', (string)$params->sayfa_boyu);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(0, 0, 0, true);
$pdf->SetAutoPageBreak(false, 0);
$font = TCPDF_FONTS::addTTFfont(__DIR__ . "/fonts/courier.ttf", 'TrueTypeUnicode', '', 96);
//$pdf->AddFont('CourierNewPSMT', '', "courier.php");

$pdf->AddPage();
//arka plan resmi ekleme kısmı
$bgFile1 = $_FILES["bg1"];
$bgFile2 = $_FILES["bg2"];

$pdf->SetAlpha(1);
$img_file1 = "images/test.jpg";
$img_file2 = "images/test.jpg";
if ($bgFile1["error"] === UPLOAD_ERR_OK)
    $img_file1 = $bgFile1["tmp_name"];
if ($bgFile1["error"] === UPLOAD_ERR_OK)
    $img_file2 = $bgFile1["tmp_name"];
if ($bgFile2["error"] === UPLOAD_ERR_OK)
    $img_file2 = $bgFile2["tmp_name"];

if ($params->sayfa_resimsiz=="true"){
    $pdf->Image($img_file1, $params->sol_marj - $params->kilavuz_bindirme, $params->ust_marj-$params->kilavuz_bindirme, $params->kart_genisligi+$params->kilavuz_bindirme*2 , $params->kart_yuksekligi+$params->kilavuz_bindirme*2, '', '', '', false, 300, '', false, false, 0);
    if ($params->sayfa_ayrı=="ayni") {
        $pdf->Image($img_file2, $params->sol_marj - $params->kilavuz_bindirme+$sagtarafyatay, $params->ust_marj-$params->kilavuz_bindirme, $params->kart_genisligi+$params->kilavuz_bindirme*2 , $params->kart_yuksekligi+$params->kilavuz_bindirme*2, '', '', '', false, 300, '', false, false, 0);
    }
}


$pdf->setPageMark();
$pdf->SetFont($font, '', 9);

if ($params->sayfa_boyu == "A4" || $params->sayfa_boyu == "A5" && $params->sayfa_ayrı=="ayri") {
 $sayfa_mirror = 210-$params->kart_genisligi;
} else {
    $sayfa_mirror = $params->sol_marj*2;
}

//barcode stil ayarları
$qrStyle = array(
    'border' => 0,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => array(255,255,255),
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
                );
//barcode yazma kısmı
$pdf->write2DBarcode($params->on_yuz_spend_txt, 'QRCODE,L', $params->sol_marj + 2, $params->ust_marj + 2, 20, 20, $qrStyle, 'N');
$pdf->write2DBarcode($params->on_yuz_Mnemonic_txt, 'QRCODE,L', $params->sol_marj + $params->kart_genisligi - 32, $params->kart_yuksekligi - 32 + $params->ust_marj, 30, 30, $qrStyle, 'N');
if ($params->sayfa_ayrı=="ayni") {
$pdf->write2DBarcode($params->arka_yuz_public_txt, 'QRCODE,L', $params->sol_marj + $sagtarafyatay + 2, $params->ust_marj + 2, 20, 20, $qrStyle, 'N');
$pdf->write2DBarcode($params->arka_yuz_view_txt, 'QRCODE,L', $params->sol_marj + $params->kart_genisligi + $sagtarafyatay - 22, $params->kart_yuksekligi - 22 + $params->ust_marj, 20, 20, $qrStyle, 'N');
}

//arkaplan rengi rgb dönüştürme işlemi
list($rr, $gg, $bb) = sscanf($params->yazi_arkaplan, "#%02x%02x%02x");

//yazı arka planı çizdirme kısmı
$pdf->SetFont($font, '', 9);
$pdf->SetAlpha($params->seffaflik_katsayisi);

$pdf->Rect($params->sol_marj + 22, $params->ust_marj + 2, 50, 17, 'DF', array(0), array($rr, $gg, $bb));
$pdf->Rect($params->sol_marj + $params->kart_genisligi - 82, $params->kart_yuksekligi - 32 + $params->ust_marj, 50, 30, 'DF', array(0), array($rr, $gg, $bb));
$pdf->Rect($params->sol_marj + $params->kart_genisligi - 32, $params->kart_yuksekligi - 34.7 + $params->ust_marj, 28, 3, 'DF', array(0), array($rr, $gg, $bb));
if ($params->sayfa_ayrı=="ayni") {
$pdf->Rect($params->sol_marj + $params->kart_genisligi + $sagtarafyatay - 82, $params->kart_yuksekligi - 21.2 + $params->ust_marj, 60, 16, 'DF', array(0), array($rr, $gg, $bb));
$pdf->Rect($params->sol_marj + $sagtarafyatay + 22, $params->ust_marj + 2, 60, 20, 'DF', array(0), array($rr, $gg, $bb));
}

$pdf->SetAlpha(1);

//yazı rengi rgb dönüştürme
list($rr, $gg, $bb) = sscanf($params->yazi_rengi, "#%02x%02x%02x");
$golge=0.15;

//bilgileri ekrana yazdırma kısmı
//ilk renk gölge rengi ilk yazı gölge ikinci renk yazı rengi ikinci yazı yazının kendisi bu şekilde gölge veriyorum.
if ($params->yazi_golge == "true") {
$pdf->SetTextColor(255-$rr, 255-$gg, 255-$bb);
$pdf->MultiCell(50, 17,$params->on_yuz_spend_txt , 0, 'j', false, 1, $params->sol_marj + 22 + $golge, $params->ust_marj + 6 + $golge, true, 0, false, true, 0, 'T', false);
}
$pdf->SetTextColor($rr, $gg, $bb);
$pdf->MultiCell(50, 17,$params->on_yuz_spend_txt , 0, 'j', false, 1, $params->sol_marj + 22, $params->ust_marj + 6, true, 0, false, true, 0, 'T', false);

$pdf->SetFont($font, '', 8);
if ($params->yazi_golge == "true") {
$pdf->SetTextColor(255-$rr, 255-$gg, 255-$bb);
$pdf->MultiCell(50, 30, $params->on_yuz_Mnemonic_txt, 0, 'J', false, 1, $params->sol_marj + $params->kart_genisligi - 82 + $golge, $params->kart_yuksekligi + $golge - 32 + $params->ust_marj, true, 0, false, true, 0, 'T', false);
}
$pdf->SetTextColor($rr, $gg, $bb);
$pdf->MultiCell(50, 30, $params->on_yuz_Mnemonic_txt, 0, 'J', false, 1, $params->sol_marj + $params->kart_genisligi - 82, $params->kart_yuksekligi - 32 + $params->ust_marj, true, 0, false, true, 0, 'T', false);

if ($params->sayfa_ayrı=="ayni") {
$pdf->SetFont($font, '', 9);
if ($params->yazi_golge == "true") {
$pdf->SetTextColor(255-$rr, 255-$gg, 255-$bb);
$pdf->MultiCell(60, 20, $params->arka_yuz_public_txt, 0, 'J', false, 1, $params->sol_marj + $sagtarafyatay + 22 + $golge, $params->ust_marj + 6 + $golge, true, 0, false, true, 0, 'T', false);
}
$pdf->SetTextColor($rr, $gg, $bb);
$pdf->MultiCell(60, 20, $params->arka_yuz_public_txt, 0, 'J', false, 1, $params->sol_marj + $sagtarafyatay + 22, $params->ust_marj + 6, true, 0, false, true, 0, 'T', false);

$pdf->SetFont($font, '', 9);
if ($params->yazi_golge == "true") {
$pdf->SetTextColor(255-$rr, 255-$gg, 255-$bb);
$pdf->MultiCell(60, 20, $params->arka_yuz_view_txt, 0, 'J', false, 1, $params->sol_marj + $params->kart_genisligi + $sagtarafyatay - 82 + $golge, $params->kart_yuksekligi - 17 + $golge + $params->ust_marj, true, 0, false, true, 0, 'T', false);
}
$pdf->SetTextColor($rr, $gg, $bb);
$pdf->MultiCell(60, 20, $params->arka_yuz_view_txt, 0, 'J', false, 1, $params->sol_marj + $params->kart_genisligi + $sagtarafyatay - 82, $params->kart_yuksekligi - 17 + $params->ust_marj, true, 0, false, true, 0, 'T', false);
}

//bilgi başlıkları yazdırma kısmı
$pdf->SetFont($font, 'B', 9);
$pdf->text($params->sol_marj + 23, $params->ust_marj+1.7, "SpendKey");
$pdf->text($params->sol_marj + $params->kart_genisligi - 32, $params->kart_yuksekligi - 35.5 + $params->ust_marj, "Mnemonic Seed");
if ($params->sayfa_ayrı=="ayni") {
$pdf->text($params->sol_marj + $sagtarafyatay + 22, $params->ust_marj + 2, "Monero Address");
$pdf->text($params->sol_marj + $params->kart_genisligi + $sagtarafyatay - 82, $params->kart_yuksekligi - 21 + $params->ust_marj, "ViewKey");
}

//klavuz çizgisi renk dönüşüm işlemi
list($rr, $gg, $bb) = sscanf($params->kilavuz_rengi, "#%02x%02x%02x");
//klavuz çizgisi stil ayarlama kısmı.
    $line_style = array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'color' => array($rr, $gg, $bb));

//klavuz çizgisi çizdirme kısmı
$pdf->line($params->sol_marj - $params->kilavuz_bindirme, $params->ust_marj, $params->sol_marj + $params->kilavuz_cizgisi, $params->ust_marj, $line_style);
$pdf->line($params->sol_marj, $params->ust_marj - $params->kilavuz_bindirme, $params->sol_marj, $params->ust_marj + $params->kilavuz_cizgisi, $line_style);
$pdf->line($params->sol_marj + $params->kart_genisligi + $params->kilavuz_bindirme, $params->ust_marj, $params->sol_marj + $params->kart_genisligi - $params->kilavuz_cizgisi, $params->ust_marj, $line_style);
$pdf->line($params->sol_marj + $params->kart_genisligi, $params->ust_marj - $params->kilavuz_bindirme, $params->sol_marj + $params->kart_genisligi, $params->ust_marj + $params->kilavuz_cizgisi, $line_style);
$pdf->line($params->sol_marj - $params->kilavuz_bindirme, $params->ust_marj + $params->kart_yuksekligi, $params->sol_marj + $params->kilavuz_cizgisi, $params->ust_marj + $params->kart_yuksekligi, $line_style);
$pdf->line($params->sol_marj, $params->kart_yuksekligi + $params->ust_marj + $params->kilavuz_bindirme, $params->sol_marj, $params->ust_marj + $params->kart_yuksekligi - $params->kilavuz_cizgisi, $line_style);
$pdf->line($params->kart_genisligi + $params->sol_marj + $params->kilavuz_bindirme, $params->ust_marj + $params->kart_yuksekligi, $params->sol_marj + $params->kart_genisligi - $params->kilavuz_cizgisi, $params->ust_marj + $params->kart_yuksekligi, $line_style);
$pdf->line($params->sol_marj + $params->kart_genisligi, $params->ust_marj + $params->kart_yuksekligi + $params->kilavuz_bindirme, $params->sol_marj + $params->kart_genisligi, $params->ust_marj + $params->kart_yuksekligi - $params->kilavuz_cizgisi, $line_style);

if ($params->sayfa_ayrı=="ayni") {
    $pdf->line($params->sol_marj + $sagtarafyatay - $params->kilavuz_bindirme, $params->ust_marj, $params->sol_marj + $params->kilavuz_cizgisi + $sagtarafyatay, $params->ust_marj, $line_style);
    $pdf->line($params->sol_marj + $sagtarafyatay, $params->ust_marj - $params->kilavuz_bindirme, $params->sol_marj + $sagtarafyatay, $params->ust_marj + $params->kilavuz_cizgisi, $line_style);
    $pdf->line($params->sol_marj + $params->kart_genisligi + $sagtarafyatay + $params->kilavuz_bindirme, $params->ust_marj, $params->sol_marj + $params->kart_genisligi - $params->kilavuz_cizgisi + $sagtarafyatay, $params->ust_marj, $line_style);
    $pdf->line($params->sol_marj + $params->kart_genisligi + $sagtarafyatay, $params->ust_marj - $params->kilavuz_bindirme, $params->sol_marj + $params->kart_genisligi + $sagtarafyatay, $params->ust_marj + $params->kilavuz_cizgisi, $line_style);
    $pdf->line($params->sol_marj + $sagtarafyatay - $params->kilavuz_bindirme, $params->ust_marj + $params->kart_yuksekligi, $params->sol_marj + $params->kilavuz_cizgisi + $sagtarafyatay, $params->ust_marj + $params->kart_yuksekligi, $line_style);
    $pdf->line($params->sol_marj + $sagtarafyatay, $params->kart_yuksekligi + $params->ust_marj + $params->kilavuz_bindirme, $params->sol_marj + $sagtarafyatay, $params->ust_marj + $params->kart_yuksekligi - $params->kilavuz_cizgisi, $line_style);
    $pdf->line($params->kart_genisligi + $params->sol_marj + $sagtarafyatay + $params->kilavuz_bindirme, $params->ust_marj + $params->kart_yuksekligi, $params->sol_marj + $params->kart_genisligi - $params->kilavuz_cizgisi + $sagtarafyatay, $params->ust_marj + $params->kart_yuksekligi, $line_style);
    $pdf->line($params->sol_marj + $params->kart_genisligi + $sagtarafyatay, $params->ust_marj + $params->kart_yuksekligi + $params->kilavuz_bindirme, $params->sol_marj + $params->kart_genisligi + $sagtarafyatay, $params->ust_marj + $params->kart_yuksekligi - $params->kilavuz_cizgisi, $line_style);
} elseif ($params->sayfa_ayrı=="ayri") {
    $pdf->AddPage();
    if ($params->sayfa_resimsiz=="true") {
        $pdf->Image($img_file2, $sayfa_mirror - $params->sol_marj - $params->kilavuz_bindirme, $params->ust_marj - $params->kilavuz_bindirme, $params->kart_genisligi + $params->kilavuz_bindirme * 2, $params->kart_yuksekligi + $params->kilavuz_bindirme * 2, '', '', '', false, 300, '', false, false, 0);
    }

    $pdf->SetAlpha($params->seffaflik_katsayisi);
    list($rr, $gg, $bb) = sscanf($params->yazi_arkaplan, "#%02x%02x%02x");
    $pdf->Rect($sayfa_mirror - $params->sol_marj + $params->kart_genisligi - 82, $params->kart_yuksekligi - 21.2 + $params->ust_marj, 60, 16, 'DF', array(0), array($rr, $gg, $bb));
    $pdf->Rect($sayfa_mirror - $params->sol_marj +  22, $params->ust_marj + 2, 60, 20, 'DF', array(0), array($rr, $gg, $bb));

    $pdf->SetAlpha(1);
    $pdf->write2DBarcode($params->arka_yuz_public_txt, 'QRCODE,H', $sayfa_mirror - $params->sol_marj + 2, $params->ust_marj + 2, 20, 20, $qrStyle, 'N');
    $pdf->write2DBarcode($params->arka_yuz_view_txt, 'QRCODE,H', $sayfa_mirror - $params->sol_marj + $params->kart_genisligi - 22, $params->kart_yuksekligi - 22 + $params->ust_marj, 20, 20, $qrStyle, 'N');

    list($rr, $gg, $bb) = sscanf($params->yazi_rengi, "#%02x%02x%02x");
    $pdf->SetFont($font, '', 9);
    if ($params->yazi_golge == "true") {
        $pdf->SetTextColor(255-$rr, 255-$gg, 255-$bb);
    $pdf->MultiCell(60, 20, $params->arka_yuz_public_txt, 0, 'J', false, 1, $sayfa_mirror - $params->sol_marj + 22 + $golge, $params->ust_marj + 6 + $golge, true, 0, false, true, 0, 'T', false);    
    }
    $pdf->SetTextColor($rr, $gg, $bb);
    $pdf->MultiCell(60, 20, $params->arka_yuz_public_txt, 0, 'J', false, 1, $sayfa_mirror - $params->sol_marj + 22, $params->ust_marj + 6, true, 0, false, true, 0, 'T', false);

    $pdf->SetFont($font, '', 9);
    if ($params->yazi_golge == "true") {
    $pdf->SetTextColor(255-$rr, 255-$gg, 255-$bb);
    $pdf->MultiCell(60, 20, $params->arka_yuz_view_txt, 0, 'J', false, 1, $sayfa_mirror - $params->sol_marj + $params->kart_genisligi - 82 + $golge, $params->kart_yuksekligi - 17 + $golge + $params->ust_marj, true, 0, false, true, 0, 'T', false);
    }
    $pdf->SetTextColor($rr, $gg, $bb);
    $pdf->MultiCell(60, 20, $params->arka_yuz_view_txt, 0, 'J', false, 1, $sayfa_mirror - $params->sol_marj + $params->kart_genisligi - 82, $params->kart_yuksekligi - 17 + $params->ust_marj, true, 0, false, true, 0, 'T', false);

    $pdf->SetFont($font, 'B', 9);
    $pdf->text($sayfa_mirror - $params->sol_marj + 22, $params->ust_marj + 2, "Monero Address");
    $pdf->text($sayfa_mirror - $params->sol_marj + $params->kart_genisligi - 82, $params->kart_yuksekligi - 21 + $params->ust_marj, "ViewKey");

    list($rr, $gg, $bb) = sscanf($params->kilavuz_rengi, "#%02x%02x%02x");
    $line_style = array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'color' => array($rr, $gg, $bb));

    $pdf->line($sayfa_mirror - $params->sol_marj - $params->kilavuz_bindirme, $params->ust_marj, $sayfa_mirror - $params->sol_marj + $params->kilavuz_cizgisi, $params->ust_marj, $line_style);
    $pdf->line($sayfa_mirror - $params->sol_marj, $params->ust_marj - $params->kilavuz_bindirme, $sayfa_mirror - $params->sol_marj, $params->ust_marj + $params->kilavuz_cizgisi, $line_style);
    $pdf->line($sayfa_mirror - $params->sol_marj + $params->kart_genisligi + $params->kilavuz_bindirme, $params->ust_marj, $sayfa_mirror - $params->sol_marj + $params->kart_genisligi - $params->kilavuz_cizgisi, $params->ust_marj, $line_style);
    $pdf->line($sayfa_mirror - $params->sol_marj + $params->kart_genisligi, $params->ust_marj - $params->kilavuz_bindirme, $sayfa_mirror - $params->sol_marj + $params->kart_genisligi, $params->ust_marj + $params->kilavuz_cizgisi, $line_style);
    $pdf->line($sayfa_mirror - $params->sol_marj - $params->kilavuz_bindirme, $params->ust_marj + $params->kart_yuksekligi, $sayfa_mirror - $params->sol_marj + $params->kilavuz_cizgisi, $params->ust_marj + $params->kart_yuksekligi, $line_style);
    $pdf->line($sayfa_mirror - $params->sol_marj, $params->kart_yuksekligi + $params->ust_marj + $params->kilavuz_bindirme, $sayfa_mirror - $params->sol_marj, $params->ust_marj + $params->kart_yuksekligi - $params->kilavuz_cizgisi, $line_style);
    $pdf->line($sayfa_mirror - $params->sol_marj + $params->kart_genisligi + $params->kilavuz_bindirme, $params->ust_marj + $params->kart_yuksekligi,$sayfa_mirror - $params->sol_marj + $params->kart_genisligi - $params->kilavuz_cizgisi, $params->ust_marj + $params->kart_yuksekligi, $line_style);
    $pdf->line($sayfa_mirror - $params->sol_marj + $params->kart_genisligi, $params->ust_marj + $params->kart_yuksekligi + $params->kilavuz_bindirme, $sayfa_mirror - $params->sol_marj + $params->kart_genisligi, $params->ust_marj + $params->kart_yuksekligi - $params->kilavuz_cizgisi, $line_style);
}

//$pdf->text(10, 80, ($params->sol_marj * 2) + ($params->kart_genisligi * 2) + $params->kart_boslugu);
//$pdf->text(10, 90, ($params->ust_marj * 2) + $params->kart_yuksekligi);

if ($params->sayfa_manual=="true") {
$pdf->AddPage("P", "a4");
$pdf->SetFont($font, ‘BI’, 12, “, ‘false’);
$pdf->Rect(0,0,210,297,'F','',$fill_color = array(255, 237, 212));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(175, 20, "This paper provides 4 pieces of information.
1-private spend key (private spend key)
2-Mnemonic Word (memory recovery key)
3-Monero Address (monero address)
4-private ViewKey (private display key)
You should hide all the data from this information, except for 3, for your security and confidentiality.

special spending key ..: is a special series of numbers required to transfer the balance at monero to a wallet. Without this key, you have no other way to save your account with the memory recovery key to spend moneroes on your account.

Mnemonic Words ..: Wallet recovery key usually consists of 25 English words. can contain more than one same word. When all words are given to monero wallet applications in the correct order, you can recover all the information about your wallet. You can carry your money by memorizing these words, going to another country completely naked.

Monero Address ..: It is the address where other people can send you monero. you can easily share it anywhere, nobody can see his balance.

private viewing key ..: BlockChain is a key required to follow the monero you send or receive in explorer. In Moneroda BlockChain explorer, nobody can see the balances and addresses.", 0, 'J', true, 1, 20, 20, true, 0, false, true, 0, 'T', false);
}

header("Content-type:application/pdf");
$pdf->Output('test.php', 'I');
