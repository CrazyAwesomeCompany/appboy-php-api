<?php

namespace CAC\AppBoy\Api;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;

class AppBoyApi
{
    /**
     * @var ClientInterface
     */
    private $guzzle;

    /**
     * @var String
     */
    private $appGroupId;


    public function __construct(ClientInterface $guzzle, $appGroupId)
    {
        $this->guzzle = $guzzle;
        $this->appGroupId = $appGroupId;
    }

    /**
     * Create the Request and parse the response data
     *
     * @param String $method
     * @param String $url
     * @param String[] $params
     * @return String[]
     */
    protected function send($method, $url, array $params = [])
    {
        $request = new Request(
            $method,
            $url,
            ['Content-type' => 'application/json'],
            json_encode($params)
        );

        $response = $this->guzzle->send($request);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Send an triggered campaign
     *
     * @param String $campaignId
     * @param String[] $users
     * @param String[] $globals
     * @return String[]
     */
    public function sendTrigger($campaignId, array $users, array $globals = [])
    {
        $splittedUsers = array_chunk($users, 50);

        $params = [
            'app_group_id' => $this->appGroupId,
            'campaign_id' => $campaignId,
        ];

        if ($globals) {
            $params['trigger_properties'] = $globals;
        }

        foreach ($splittedUsers as $userChunk) {
            $params['recipients'] = $userChunk;

            $response = $this->send('POST', '/campaigns/trigger/send', $params);
        }

        return $response;
    }

    /**
     * Start tracking users in AppBoy
     *
     * @param String[] $users
     * @return String
     */
    public function trackUsers(array $users)
    {
        $params = [
            'app_group_id' => $this->appGroupId,
            'attributes' => $users,
        ];

        $response = $this->send('POST', '/users/track', $params);

        return $response;
    }

    /**
     * Remove AppBoy users by their external ids.
     *
     * @param String[] $users List of AppBoy external ids
     * @return String
     */
    public function deleteUsers(array $users)
    {
        $params = [
            'app_group_id' => $this->appGroupId,
            'external_ids' => $users,
        ];

        return $this->send('POST', '/users/delete', $params);
    }

    /**
     * Get the AppBoy email unsubscriptions
     *
     * @param \DateTime $start
     * @param \DateTime $end
     * @param number $offset
     * @return String
     */
    public function getUnsubscriptions(\DateTime $start = null, \DateTime $end = null, $limit = 500, $offset = 0)
    {
        if (null === $start) {
            $start = new \DateTime();
            $start->sub(new \DateInterval('P1W'));
        }

        if (null === $end) {
            $end = new \DateTime();
        }

        $params = [
            'app_group_id' => $this->appGroupId,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'limit' => $limit,
            'offset' => $offset,
        ];

        return $this->send('POST', '/email/unsubscribes', $params);
    }
}

