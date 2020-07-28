<?php
class PrensaDeSubmissoesPKP implements PrensaDeSubmissoes {

    private $logoParaFolhaDeRosto;
    private $submissão;
    private $tradutor;

    public function __construct(string $logoParaFolhaDeRosto, Submissao $submissão, Tradutor $tradutor) {
        $this->logoParaFolhaDeRosto = $logoParaFolhaDeRosto;
        $this->submissão = $submissão;
        $this->tradutor = $tradutor;
    }

    public function inserirNoBanco($id, $json){
        $fileSettingsDAO = new SubmissionFileSettingsDAO(); 
        DAORegistry::registerDAO('SubmissionFileSettingsDAO', $fileSettingsDAO);
        
        $fileSettingsDAO->updateSetting($id, 'folhaDeRosto', 'sim', 'string', false);
        $fileSettingsDAO->updateSetting($id, 'revisoes', $json, 'JSON', false);
    }

    private function verificaFolhaNoBanco($folhaDeRosto, $composição) {
        $fileSettingsDAO = new SubmissionFileSettingsDAO(); 
        DAORegistry::registerDAO('SubmissionFileSettingsDAO', $fileSettingsDAO);

        $pdf = new Pdf($composição->arquivo);
        $id = $composição->identificador;
        $revisão = $composição->revisão;

        $setting = $fileSettingsDAO->getSetting($id, 'folhaDeRosto');
        $revisões = '[]';

        if($setting == 'sim') {     
            $revisões = $fileSettingsDAO->getSetting($id, 'revisoes');
            $folhaDeRosto->remover($pdf);
        }

        $revisões = json_decode($revisões);
        array_push($revisões, $revisão);
        $revisõesJSON = json_encode($revisões);

        return $revisõesJSON;
    }

    public function inserirFolhasDeRosto(): void {
        foreach($this->submissão->obterComposições() as $composição) {
            $folhaDeRosto = new FolhaDeRosto($this->submissão, $this->logoParaFolhaDeRosto, $composição->locale, $this->tradutor);

            if (Pdf::éPdf($composição->arquivo)) {
                $pdf = new Pdf($composição->arquivo);
                $id = $composição->identificador;
                $revisõesJSON = $this->verificaFolhaNoBanco($folhaDeRosto, $composição);
                $folhaDeRosto->inserir($pdf);
                $this->inserirNoBanco($id, $revisõesJSON);
            }
        }   
    }
}