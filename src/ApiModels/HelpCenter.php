<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Help Center (portals and their categories / articles).
 *
 * @see https://developers.chatwoot.com/api-reference/help-center
 */
final class HelpCenter extends BaseModel
{
    /**
     * List portals.
     *
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function listPortals(): Collection
    {
        return $this->httpGet($this->accountPath('portals'));
    }

    /**
     * Create a portal.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'slug'=>…, 'custom_domain'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function createPortal(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('portals'), $attributes);
    }

    /**
     * Update a portal.
     *
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function updatePortal(int $portalId, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("portals/{$portalId}"), $attributes);
    }

    /**
     * Create a category within a portal.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'slug'=>…, 'locale'=>'en']
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function createCategory(int $portalId, array $attributes): Collection
    {
        return $this->httpPost($this->accountPath("portals/{$portalId}/categories"), $attributes);
    }

    /**
     * Create an article within a portal.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['title'=>…, 'content'=>…, 'category_id'=>…, 'author_id'=>…]
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function createArticle(int $portalId, array $attributes): Collection
    {
        return $this->httpPost($this->accountPath("portals/{$portalId}/articles"), $attributes);
    }
}
