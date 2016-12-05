<?php

namespace AppBundle\Utils;

use Symfony\Component\Form\Form;

/**
 * Class FormError
 * @package AppBundle\Utils
 */
class FormError
{
    /**
     * @param Form $form
     * @return array
     */
    public function toArray(Form $form)
    {
        $errors = [];
        $errorIterator = $form->getErrors(true);
        while ($errorIterator->valid()) {
            $current = $errorIterator->current();
            $errors[$current->getOrigin()->getName()] = $current->getMessage();
            $errorIterator->next();
        }

        return $errors;
    }
}
