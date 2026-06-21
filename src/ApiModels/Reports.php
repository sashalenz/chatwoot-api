<?php

declare(strict_types=1);

namespace Sashalenz\ChatwootApi\ApiModels;

use Illuminate\Support\Collection;
use Sashalenz\ChatwootApi\Exceptions\ChatwootApiException;

/**
 * Application API — Reports. Unlike the rest of the API these live under the
 * v2 prefix: `/api/v2/accounts/{account_id}/reports/…`.
 *
 * @see https://developers.chatwoot.com/api-reference/reports
 */
final class Reports extends BaseModel
{
    /**
     * Account-level metric series (`metric`, `type`, `since`, `until`, …).
     *
     * @param  array<string,mixed>  $query
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function account(array $query = []): Collection
    {
        return $this->httpGet($this->reportsPath(''), $query);
    }

    /**
     * Account summary (totals over a period).
     *
     * @param  array<string,mixed>  $query
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function summary(array $query = []): Collection
    {
        return $this->httpGet($this->reportsPath('summary'), $query);
    }

    /**
     * Conversation metrics (grouped by agent / inbox / team / …).
     *
     * @param  array<string,mixed>  $query
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function conversations(array $query = []): Collection
    {
        return $this->httpGet($this->reportsPath('conversations'), $query);
    }

    /**
     * First response time distribution by channel.
     *
     * @param  array<string,mixed>  $query
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function firstResponseTimeDistribution(array $query = []): Collection
    {
        return $this->httpGet($this->reportsPath('first_response_time_distribution'), $query);
    }

    /**
     * Inbox × label matrix report.
     *
     * @param  array<string,mixed>  $query
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function inboxLabelMatrix(array $query = []): Collection
    {
        return $this->httpGet($this->reportsPath('inbox_label_matrix'), $query);
    }

    /**
     * Outgoing messages count, grouped by `group_by` (e.g. agent / inbox).
     *
     * @param  array<string,mixed>  $query
     * @return Collection<string,mixed>
     *
     * @throws ChatwootApiException
     */
    public function outgoingMessagesCount(array $query = []): Collection
    {
        return $this->httpGet($this->reportsPath('outgoing_messages_count'), $query);
    }

    /**
     * Build a v2 reports path, e.g. reportsPath('summary') →
     * `api/v2/accounts/42/reports/summary`. Empty suffix → `.../reports`.
     *
     * @throws ChatwootApiException
     */
    private function reportsPath(string $suffix): string
    {
        $base = 'api/v2/accounts/'.$this->resolveAccountId().'/reports';

        return $suffix === '' ? $base : $base.'/'.ltrim($suffix, '/');
    }
}
