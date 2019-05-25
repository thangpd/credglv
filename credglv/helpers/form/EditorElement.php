<?php
/**
 * @project  edu
 * @copyright © 2017 by ivoglent
 * @author ivoglent
 * @time  8/2/17.
 */


namespace credglv\helpers\form;


use AdamWathan\Form\Elements\FormControl;


class EditorElement extends FormElement
{
    private $options = [];
    public function __construct(FormControl $control, array $configs = [])
    {
        $this->options = $configs;
        parent::__construct($control, $configs);
    }

    /**
     * Generate wordpress editor
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function render()
    {
        $html = '';
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        try {
            $editorId = @$this->options['id'];
            if (empty($editorId)) {
                $editorId = credglv()->helpers->general->getRandomString(8);
            }
            echo '<div class="la-form-group">';
            echo "<label>{$this->label}</label>";
            $value = @$this->options['value'];
            wp_editor( $value, $editorId ,[
                'textarea_name' => $this->options['name']
            ]);
            echo '</div>';
            $html = ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
        return $html;
    }
}