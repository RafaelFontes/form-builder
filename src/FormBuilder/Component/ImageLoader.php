<?php

namespace FormBuilder\Component;

use FormBuilder\Entity\Component;

class ImageLoader extends Component {

    protected $length = 50;
    protected $type = "VARCHAR";

    /**
     * @param \stdClass $properties
     * @return void
     */
    public function loadProperties(\stdClass $properties)
    {
        $this->properties = $properties;
        foreach($properties as $name => $value)
        {
            switch($name)
            {
                case "name" :
                    $this->name = $value;
                    break;
                case "required":
                    $this->nullable = !$value;
                    break;
                case "id":
                    $this->id = $value;
                    break;
            }
        }
    }

    public function toHtmlField(array $json = null)
    {
        $html = '
        <div class="controls form-group">
            <div id="dropzone"  class="dropzone"> Arraste aqui <i class="icon-download-alt pull-right"></i> </div>
            <div class="fileupload-progress fade">
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <div class="progress-extended">&nbsp;</div>
            </div>
            <div class="form-actions fileupload-buttonbar no-margin">
                <span class="btn btn-sm btn-default fileinput-button">
                    <i class="icon-plus"></i>
                    <span>Adicionar arquivo</span>
                    <input type="file" id="'.$this->name.'" name="'.$this->name.'" />
                </span>
                <button type="submit" class="btn btn-primary btn-sm start">
                    <i class="icon-upload"></i>
                    <span>Enviar</span>
                </button>
                <button type="reset" class="btn btn-inverse btn-sm cancel">
                    <i class="icon-ban-circle"></i>
                    <span>Cancelar envio</span>
                </button>
            </div>
            <div class="fileupload-loading"><i class="icon-spin icon-spinner"></i></div>
            <!-- The table listing the files available for upload/download -->
            <table role="presentation" class="table table-striped">
                <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
            </table>
        </div>';

        $html .= '<script>
            $(fucntion(){
                var $fileupload = $("#'.$this->name.'");
                    $fileupload.fileupload({
                        allowedExtensions : ["jpg", "jpeg"],
                        showCropTool: 1,
                        sizeLimit: 10 * 1024 * 1024,
                        // Uncomment the following to send cross-domain cookies:
                        //xhrFields: {withCredentials: true},
                        url: "/upload.json"
                        dropZone: $(".dropzone")
                    });
            });
        </script>';

        return $html;
    }

} 