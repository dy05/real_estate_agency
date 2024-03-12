<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class BanWord extends Constraint
{
    /**
     * @param string $message
     * @param array $banWords
     * @param mixed|null $options
     * @param array|null $groups
     * @param mixed|null $payload
     */
    public function __construct(
        public string $message = 'This contains a banned word "{{ banWord }}".',
        public array $banWords = ['spam', 'viagra'],
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
    )
    {
        parent::__construct($options, $groups, $payload);
    }
}
