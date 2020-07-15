<?php

namespace Yard\DigiD\Fields;

use function Yard\DigiD\Foundation\Helpers\view;

class TextField extends AbstractField
{
    /**
     * @param object $field
     * @param array $value
     */
    public function __construct(object $field, array $value)
    {
        parent::__construct($field, $value);
    }

    /**
     * Get the structured input.
     *
     * @return string
     */
    protected function getInputField(): string
    {
        return view('digid/no-certificates.php');
    }

    /**
     * Render the input.
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->is_admin || ! \rgar($this->getInput(), 'isHidden')) {
            return "{$this->getSpanField()}
                        {$this->getInputField()}
                    </span>";
        }
    }
}
