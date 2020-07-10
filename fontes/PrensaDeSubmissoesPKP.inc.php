<?php
class PrensaDeSubmissoesPKP {

    private $logoParaFolhaDeRosto;
    private $submissão;
    private $tradutor;

    public function __construct(string $logoParaFolhaDeRosto, Submissao $submissão, Tradutor $tradutor) {
        $this->logoParaFolhaDeRosto = $logoParaFolhaDeRosto;
        $this->submissão = $submissão;
        $this->tradutor = $tradutor;
    }

    public function inserirFolhasDeRosto(): void {
       foreach($this->submissão->obterComposições() as $composição){
           $folhaDeRosto = new FolhaDeRosto($this->submissão, $this->logoParaFolhaDeRosto, $composição->locale, $this->tradutor);

           if (Pdf::éPdf($composição->arquivo)) {
               $fileSettingsDAO = new SubmissionFileSettingsDAO(); 
               DAORegistry::registerDAO('SubmissionFileSettingsDAO', $fileSettingsDAO);
               
               $pdf = new Pdf($composição->arquivo);
               $id = $composição->identificador;
               $revisão = $composição->revisão;

               $revisoes = $fileSettingsDAO->getSetting($id, 'revisoes');
               $revisoes = json_decode($revisoes);
               array_push($revisoes, $revisão);
               $objJSON = json_encode($revisoes);
               
               $fileSettingsDAO->updateSetting($id, 'revisoes', $objJSON, 'JSON', false);
               
               $setting = $fileSettingsDAO->getSetting($id, 'folhaDeRosto');
               
               if($setting == 'sim'){
                   error_log("Já tem folha de rosto");
                //    $folhaDeRosto->remover($pdf);
                //    $folhaDeRosto->inserir($pdf);
               }
           }
       }   
    }
}