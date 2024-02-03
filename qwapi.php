<?php
namespace meigo\QuickWinAPI;

// DFFI
use system\DFFI;
use system\DFFIType;
use system\DFFIStruct;
// DevelNext
use php\lang\Environment;
use php\io\Stream;
use php\io\IOException;
use php\lib\str;

/**
 * Class qwapi
 * @package QuickWinAPI
 */

class qwapi
{
	
    function __construct(UXEvent $e = null)
    {    
	echo "[ERROR] Невозможно!\n";  
     	   alert('Продолжение работы программы невозможно. Установите DFFI 1.0.1');
		app()->shutdown();
		exit;
    }
	
    /**
     * Returns a text of safe string
     * @return string
     */
	public function MessageBoxA($hwnd, $title, $message, $type)
	{
		$user32 = new DFFI("user32");
		$user32->bind("MessageBoxA", "int", ["int", "string", "string", "int"]);

		DFFI::MessageBoxA($hwnd, $message, $title, $type);
	}
	
	public function MessageBoxExA($hwnd, $title, $message, $type, $wLanguageId)
	{
		$user32 = new DFFI("user32");
		$user32->bind("MessageBoxA", "int", ["int", "string", "string", "int", "int"]);

		DFFI::MessageBoxA($hwnd, $message, $title, $type, $wLanguageId);
	}
	
	public function GetCursorPos()
	{
		$user32 = new DFFI("user32");
		$user32->bind("GetCursorPos", DFFIType::INT, [DFFIType::STRUCT]);

		$point = new DFFIStruct("POINT", ["int", "int"]);
		DFFI::GetCursorPos($point);
		return $point->getResponse(); // Example: print_r((new qwapi)->GetCursorPos());
	}

	
	public function SetCursorPos($x,$y)
	{
		$user32 = new DFFI("user32");
		$user32->bind("SetCursorPos", DFFIType::INT, ["int", "int"]);
        
		DFFI::SetCursorPos($x,$y);
	}
	
	public function GetScreenshot()
	{
		echo "[INFO] | Making...\n";
		//Описываем функции
		$user32 = new DFFI("user32");
		$user32->bind("OpenClipboard", DFFIType::BOOL, [DFFIType::INT]);
		$user32->bind("EmptyClipboard", DFFIType::BOOL, []);
		$user32->bind("SetClipboardData", DFFIType::BOOL, [DFFIType::INT, DFFIType::INT]);
		$user32->bind("CloseClipboard", DFFIType::BOOL, []);

		$gdi32 = new DFFI("gdi32");
		$gdi32->bind("CreateDCA", DFFIType::INT, [DFFIType::STRING, DFFIType::INT, DFFIType::INT, DFFIType::INT]);
		$gdi32->bind("CreateCompatibleDC", DFFIType::INT, [DFFIType::INT]);
		$gdi32->bind("GetDeviceCaps", DFFIType::INT, [DFFIType::INT, DFFIType::INT]);
		$gdi32->bind("CreateCompatibleBitmap", DFFIType::INT, [DFFIType::INT, DFFIType::INT, DFFIType::INT]);
		$gdi32->bind("SelectObject", DFFIType::INT, [DFFIType::INT, DFFIType::INT]);
		$gdi32->bind("BitBlt", DFFIType::BOOL, [DFFIType::INT, DFFIType::INT, DFFIType::INT, DFFIType::INT, DFFIType::INT, DFFIType::INT, DFFIType::INT, DFFIType::INT, DFFIType::INT]);
		$gdi32->bind("DeleteDC", DFFIType::BOOL, [DFFIType::INT]);


		//Вызываем ф-ии
		$hScreenDC = DFFI::CreateDCA("DISPLAY", 0, 0, 0);
		$hMemoryDC = DFFI::CreateCompatibleDC($hScreenDC);

		$width = DFFI::GetDeviceCaps($hMemoryDC, 8);
		$height = DFFI::GetDeviceCaps($hMemoryDC, 10);

		$hBitmap = DFFI::CreateCompatibleBitmap($hScreenDC, $width, $height);
		$hOldBitmap = DFFI::SelectObject($hMemoryDC, $hBitmap);

		DFFI::BitBlt($hMemoryDC, 0, 0, $width, $height, $hScreenDC, 0, 0, 0x00CC0020);
		DFFI::SelectObject($hMemoryDC, $hOldBitmap);

		DFFI::OpenClipboard(0);
		DFFI::EmptyClipboard();
		DFFI::SetClipboardData(2, $hBitmap);
		DFFI::CloseClipboard();

		DFFI::DeleteDC($hMemoryDC);
	}
	
}