<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\UtilsBundle\Event;

class ExtendEntityFinderEvent extends AbstractEvent
{
    /** @var string */
    private $table;

    /** @var int|string */
    private $id;

    /** @var string|null */
    private $output = null;
    /**
     * @var array
     */
    private $parents;
    /**
     * @var bool
     */
    private $onlyText;
    /**
     * @var array
     */
    private $inserttags;

    public function __construct(string $table, $id, array $parents, array $inserttags, bool $onlyText = false)
    {
        $this->table = $table;
        $this->id = $id;
        $this->parents = $parents;
        $this->onlyText = $onlyText;
        $this->inserttags = $inserttags;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    public function addParent(string $table, $id): void
    {
        $this->parents[] = ['table' => $table, 'id' => $id];
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    public function setParents(array $parents): void
    {
        $this->parents = $parents;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(?string $output): void
    {
        $this->output = $output;
    }

    public function isOnlyText(): bool
    {
        return $this->onlyText;
    }

    public function addInserttag(string $inserttag): void
    {
        $this->inserttags[] = $inserttag;
    }

    public function getInserttags(): array
    {
        return $this->inserttags;
    }

    public function setInserttags(array $inserttags): void
    {
        $this->inserttags = $inserttags;
    }
}
