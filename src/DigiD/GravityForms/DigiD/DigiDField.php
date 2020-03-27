<?php

namespace Yard\DigiD\GravityForms\DigiD;

use GF_Field;
use function Yard\DigiD\Foundation\Helpers\config;

use function Yard\DigiD\Foundation\Helpers\resolve;
use Yard\DigiD\Foundation\Plugin;
use Yard\DigiD\GravityForms\DigiD\Inputs\LinkInput;
use Yard\DigiD\GravityForms\DigiD\Inputs\TextInput;

if (! class_exists('\GFForms')) {
    die();
}

class DigiDField extends GF_Field
{
    /**
     * @var string $type The field type.
     */
    public $type = 'digid';

    /** @var ?string */
    protected $bsn;

    public function __construct($data = [])
    {
        $this->bsn = resolve('session')->get('hsw_bsn');
        parent::__construct($data);
    }

    /**
     * Return the field title, for use in the form editor.
     *
     * @return string
     */
    public function get_form_editor_field_title()
    {
        return esc_attr__('DigiD', config('core.text_domain'));
    }

    /**
     * Returns the field button properties for the form editor. The array contains two elements:
     * 'group' => 'standard_fields' // or  'advanced_fields', 'post_fields', 'pricing_fields'
     * 'text'  => 'Button text'
     *
     * Built-in fields don't need to implement this because the buttons are added in sequence in GFFormDetail
     *
     * @return array
     */
    public function get_form_editor_button()
    {
        return [
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title(),
        ];
    }

    /**
     * Returns the class names of the settings which should be available on the field in the form editor.
     *
     * @return array
     */
    public function get_form_editor_field_settings()
    {
        return [
            'sub_label_placement_setting',
            'input_placeholders_setting',
            'rules_setting',
            'conditional_logic_field_setting',
            'label_setting',
            'rules_setting',
            'description_setting',
            'css_class_setting',
        ];
    }

    /**
     * This field type can be used when configuring conditional logic rules.
     *
     * @return bool
     */
    public function is_conditional_logic_supported(): bool
    {
        return true;
    }

    /**
     * Override this method to perform custom validation logic.
     *
     * Return the result (bool) by setting $this->failed_validation.
     * Return the validation message (string) by setting $this->validation_message.
     *
     * @param string|array $value The field value from get_value_submission().
     * @param array        $form  The Form Object currently being processed.
     *
     * @return void
     */
    public function validate($value, $form)
    {
    }

    /**
     * Return all the fields available.
     *
     * @param array $value
     *
     * @return []
     */
    protected function getFields(array $value): array
    {
        return [
            (new LinkInput($this, $value, Plugin::getInstance()->getContainer()->get('digid')))
                ->setFieldID(2)
                ->setFieldName('digid')
                ->setFieldText(__('DigiD', config('core.text_domain'))),
            (new TextInput($this, $value))
                ->setFieldID(1)
                ->setFieldName('bsn')
                ->setValue($this->bsn)
        ];
    }

    /**
     * Returns the field inner markup.
     *
     * @param array        $form  The Form Object currently being processed.
     * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
     * @param null|array   $entry Null or the Entry Object currently being edited.
     *
     * @return string
     */
    public function get_field_input($form, $value = '', $entry = null)
    {
        $output = implode(' ', array_map(function ($item) {
            return $item->render();
        }, $this->getFields($value)));

        return "<div class='ginput_complex{$this->class_suffix} ginput_container ginput_container_digid' id='input_{$form['id']}_{$this->id}'>
					{$output}
                <div class='gf_clear gf_clear_complex'></div>
            </div>";
    }

    /**
     * Returns the scripts to be included for this field type in the form editor.
     *
     * @return string
     */
    public function get_form_editor_inline_script_on_page_render()
    {

        // set the default field label for the field
        $script = sprintf("function SetDefaultValues_%s(field) {
        field.label = '%s';
        field.inputs = [
            new Input(field.id + '.1', '%s'),
        ];
        }", $this->type, $this->get_form_editor_field_title(), 'BSN') . PHP_EOL;

        return $script;
    }

    /**
     * Format the entry value for display on the entry detail page and for the {all_fields} merge tag.
     *
     * Return a value that's safe to display for the context of the given $format.
     *
     * @param string|array $value    The field value.
     * @param string       $currency The entry currency code.
     * @param bool|false   $use_text When processing choice based fields should the choice text be returned instead of the value.
     * @param string       $format   The format requested for the location the merge is being used. Possible values: html, text or url.
     * @param string       $media    The location where the value will be displayed. Possible values: screen or email.
     *
     * @return string
     */
    public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen')
    {
        if (is_array($value)) {
            $bsn                           = trim(rgget($this->id . '.1', $value));

            $return = $bsn;
        } else {
            $return = '';
        }

        if ('html' === $format) {
            $return = esc_html($return);
        }

        return $return;
    }
}
