<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Contracts;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @method static array  getFields()
 * @method static array  getFilters()
 * @method static array  getRelationships()
 * @method static array  getSorts()
 * @method static string getModel()
 */
interface ResourceInterface
{
    /**
     * Get the type of the resource.
     */
    public function getType(): string;

    /**
     * Get the primary identifier for the resource.
     */
    public function getId(): string;

    /**
     * Get all the loaded attributes for the resource.
     */
    public function getAttributes(): array;

    /**
     * Get all the loaded relations for the resource.
     */
    public function getRelations(): array;

    /**
     * Return the resource as an array.
     */
    public function toArray(): array;
}
