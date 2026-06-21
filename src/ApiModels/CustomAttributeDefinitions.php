<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\CustomAttributeDefinitionData;
use Sashalenz\ChatwootApi\Data\Paginated;
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
     * @return Paginated<CustomAttributeDefinitionData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('custom_attribute_definitions'))->all(), CustomAttributeDefinitionData::class);
    }

    /**
     * @throws ChatwootApiException
     */
    public function get(int $id): CustomAttributeDefinitionData
    {
        return CustomAttributeDefinitionData::from($this->httpGet($this->accountPath("custom_attribute_definitions/{$id}"))->all());
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['attribute_display_name'=>…, 'attribute_key'=>…, 'attribute_model'=>'conversation_attribute|contact_attribute', 'attribute_display_type'=>'text|number|…']
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): CustomAttributeDefinitionData
    {
        return CustomAttributeDefinitionData::from($this->httpPost($this->accountPath('custom_attribute_definitions'), $attributes)->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $id, array $attributes): CustomAttributeDefinitionData
    {
        return CustomAttributeDefinitionData::from($this->httpPatch($this->accountPath("custom_attribute_definitions/{$id}"), $attributes)->all());
    }

    /**
     * Delete a custom attribute definition. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $id): bool
    {
        $this->httpDelete($this->accountPath("custom_attribute_definitions/{$id}"));

        return true;
    }
}
