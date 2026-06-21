<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\ArticleData;
use Sashalenz\ChatwootApi\Data\CategoryData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Data\PortalData;
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
     * @return Paginated<PortalData>
     *
     * @throws ChatwootApiException
     */
    public function listPortals(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('portals'))->all(), PortalData::class);
    }

    /**
     * Create a portal.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'slug'=>…, 'custom_domain'=>…]
     *
     * @throws ChatwootApiException
     */
    public function createPortal(array $attributes): PortalData
    {
        return PortalData::from($this->httpPost($this->accountPath('portals'), $attributes)->all());
    }

    /**
     * Update a portal.
     *
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function updatePortal(int $portalId, array $attributes): PortalData
    {
        return PortalData::from($this->httpPatch($this->accountPath("portals/{$portalId}"), $attributes)->all());
    }

    /**
     * Create a category within a portal.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['name'=>…, 'slug'=>…, 'locale'=>'en']
     *
     * @throws ChatwootApiException
     */
    public function createCategory(int $portalId, array $attributes): CategoryData
    {
        return CategoryData::from($this->httpPost($this->accountPath("portals/{$portalId}/categories"), $attributes)->all());
    }

    /**
     * Create an article within a portal.
     *
     * @param  array<string,mixed>  $attributes  e.g. ['title'=>…, 'content'=>…, 'category_id'=>…, 'author_id'=>…]
     *
     * @throws ChatwootApiException
     */
    public function createArticle(int $portalId, array $attributes): ArticleData
    {
        return ArticleData::from($this->httpPost($this->accountPath("portals/{$portalId}/articles"), $attributes)->all());
    }
}
