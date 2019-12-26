<?php

namespace App;

use TCPDF as PDF;

use URL;
use Auth;

class MyPdf extends PDF {

	//Page header
	public function Header() {
			// Logo
        if(Auth::user()->company && Auth::user()->company->logo)
        {
            $file = json_decode(Auth::user()->company->logo, true);
            $image_file = URL::asset('/storage/' . $file[0]['download_link']);
            //$image_file = URL::asset('img/logo.png');
            $this->Image($image_file, 10, 10, 15, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);

        }
			// Set font
			$this->SetFont('helvetica', 'B', 16);
			$this->SetTextColor(129,127,127);

			$company_name = Auth::user()->company ? Auth::user()->company->company_name : '';
			// Title
			$this->Cell(0, 15, $company_name, 0, false, 'C', 0, '', 0, false, 'M', 'M');
	}

	// Page footer
	public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			$this->Cell(0, 10, 'Your initials here', 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}

}
