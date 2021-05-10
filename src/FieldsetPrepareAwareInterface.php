<?php

namespace Laminas\Form;

/**
 * Deprecated by https://github.com/zendframework/zf2/pull/5636
 *
 * @deprecated
 */
interface FieldsetPrepareAwareInterface
{
    /**
     * Prepare the fieldset element (called while this fieldset is added to another one)
     *
     * @return mixed
     */
    public function prepareFieldset();
}
