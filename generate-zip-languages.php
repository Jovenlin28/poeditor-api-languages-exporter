<?php
// A PHP Script to read a list of languages in the particular project in poeditor.com.
// Exports each language file and automatically creates a zip file containing each exported language file.
// Downloads the zip file.

class LanguageTransExporter
{

    private $api_token;
    private $project_id;
    private $format;
    private $export_api;
    private $languages_api;

    public function __construct($api_token, $project_id, $format)
    {
        $this->api_token = $api_token;
        $this->project_id = $project_id;
        $this->format = $format;
        $this->export_api = "https://api.poeditor.com/v2/projects/export";
        $this->languages_api = 'https://api.poeditor.com/v2/languages/list';
    }

    public function exportAllLanguages($languages)
    {
        $downloadableLinks = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->export_api);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        foreach ($languages as $lang) {
            $postData = "api_token=" . $this->api_token . "&id=" . $this->project_id . "&type=" . $this->format . "&language=" . $lang->code;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            $res = curl_exec($ch);
            array_push($downloadableLinks, array('languageCode' => $lang->code, 'url' => json_decode($res)->result->url));
        }
        curl_close($ch);

        $this->generateZip($downloadableLinks);
    }

    public function getLanguages()
    {
        $postData = "api_token=" . $this->api_token . "&id=" . $this->project_id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->languages_api);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);

        curl_close($ch);

        return json_decode($res)->result->languages;
    }

    private function generateZip($downloadableLinks)
    {
        $zip = new ZipArchive;
        $zipFilename = 'languages.zip';
        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($downloadableLinks as $link) {
                $content = file_get_contents($link['url']);
                $zip->addFromString($link['languageCode'] . '.' . $this->format, $content);
            }
            $zip->close();

            // Download the created zip file
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename = $zipFilename");
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile("$zipFilename");
        }
    }
}

$api_token = $_POST['api_token'];
$project_id = $_POST['project_id'];
$format = $_POST['format'];

$langTransExporter = new LanguageTransExporter($api_token, $project_id, $format);

$languages = $langTransExporter->getLanguages();

$langTransExporter->exportAllLanguages($languages);
