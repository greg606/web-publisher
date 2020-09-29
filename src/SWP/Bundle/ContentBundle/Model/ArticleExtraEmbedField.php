<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

class ArticleExtraEmbedField extends ArticleExtraField implements ArticleExtraEmbedFieldInterface
{
    /** @var string */
    protected $embed;

    /** @var string */
    protected $description;

    private function __construct(string $key, array $value)
    {
        $this->setFieldName($key);
        $this->setEmbed($value['embed']);
        $this->setDescription($value['description']);
    }

    public static function newFromValue(string $key, array $value): ArticleExtraEmbedField
    {
        return new self($key, $value);
    }

    public function setEmbed(?string $embed): void
    {
        $this->embed = $embed;
    }

    public function getEmbed(): string
    {
        return $this->embed;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function toApiFormat(): array
    {
        return [
            'embed' => $this->embed,
            'description' => $this->description,
        ];
    }
}
