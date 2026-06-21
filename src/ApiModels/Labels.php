<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Sashalenz\ChatwootApi\Data\LabelData;
use Sashalenz\ChatwootApi\Data\Paginated;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Labels (account-level label catalogue).
 *
 * @see https://developers.chatwoot.com/api-reference/labels
 */
final class Labels extends BaseModel
{
    /**
     * @return Paginated<LabelData>
     *
     * @throws ChatwootApiException
     */
    public function list(): Paginated
    {
        return Paginated::fromResponse($this->httpGet($this->accountPath('labels'))->all(), LabelData::class);
    }

    /**
     * @throws ChatwootApiException
     */
    public function get(int $labelId): LabelData
    {
        return LabelData::from($this->httpGet($this->accountPath("labels/{$labelId}"))->all());
    }

    /**
     * @param  array<string,mixed>  $attributes  e.g. ['title'=>…, 'description'=>…, 'color'=>'#FF0000', 'show_on_sidebar'=>true]
     *
     * @throws ChatwootApiException
     */
    public function create(array $attributes): LabelData
    {
        return LabelData::from($this->httpPost($this->accountPath('labels'), $attributes)->all());
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws ChatwootApiException
     */
    public function update(int $labelId, array $attributes): LabelData
    {
        return LabelData::from($this->httpPatch($this->accountPath("labels/{$labelId}"), $attributes)->all());
    }

    /**
     * Delete a label. Returns true on success.
     *
     * @throws ChatwootApiException
     */
    public function delete(int $labelId): bool
    {
        $this->httpDelete($this->accountPath("labels/{$labelId}"));

        return true;
    }
}
