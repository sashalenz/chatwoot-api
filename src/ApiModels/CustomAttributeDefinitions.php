<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Custom Attribute Definitions (the schema of custom
 * attributes available on conversations / contacts).
 *
 * @see https://developers.chatwoot.com/api-reference/custom-attributes
 */
final class CustomAttributeDefinitions extends BaseModel
{
    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function list(): Collection
    {
        return $this->httpGet($this->accountPath('custom_attribute_definitions'));
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function get(int $id): Collection
    {
        return $this->httpGet($this->accountPath("custom_attribute_definitions/{$id}"));
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['attribute_display_name'=>…, 'attribute_key'=>…, 'attribute_model'=>'conversation_attribute|contact_attribute', 'attribute_display_type'=>'text|number|…']
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): Collection
    {
        return $this->httpPost($this->accountPath('custom_attribute_definitions'), $attributes);
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function update(int $id, array $attributes): Collection
    {
        return $this->httpPatch($this->accountPath("custom_attribute_definitions/{$id}"), $attributes);
    }

    /**
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function delete(int $id): Collection
    {
        return $this->httpDelete($this->accountPath("custom_attribute_definitions/{$id}"));
    }
}
