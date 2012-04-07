<?php
class StreamService implements HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Streams';
    }

    public function getResourceNameSingularized()
    {
        return 'Stream';
    }

    public function convertResourceToJson(Stream $stream)
    {
        return json_encode(array(
            'id' => $stream->getId(),
            'name' => $stream->getName(),
            'links' => array(
                array(
                    'rel' => 'activities',
                    'href' => Config::get('endpoint_base_url') . 'activity?stream_id=' . urlencode($stream->getId())
                ),
                array(
                    'rel' => 'subscribers',
                    'href' => Config::get('endpoint_base_url') . 'subscription?stream_id=' . urlencode($stream->getId())
                )
            )
        ));
    }

    /**
     * @return Stream[]
     */
    public function getStreams(array $values)
    {
        $db_service = Services::get('Database');

        $streams = array();
        foreach ($db_service->getTableRows('activity_streams') as $row)
        {
            $streams[] = new Stream($row);
        }

        return $streams;
    }

    /**
     * @return Stream
     */
    public function getStream($stream_id, array $values = array())
    {
        $db_service = Services::get('Database');
        $row = $db_service->getTableRow('activity_streams', 'id = ?', array($stream_id));
        return new Stream($row);
    }

    /**
     * @return Stream
     */
    public function deleteStream($stream_id, array $values = array())
    {
        $db_service = Services::get('Database');

        $stream = $this->getStream($stream_id);

        $db_service->deleteTableRows('activity_stream_subscriptions', 'stream_id = ?', array($stream_id));
        $db_service->deleteTableRows('activity_stream_unsubscriptions', 'stream_id = ?', array($stream_id));
        $db_service->deleteTableRows('activities', 'stream_id = ?', array($stream_id));

        $db_service->deleteTableRow('activity_streams', 'id = ?', array($stream_id));

        return $stream;
    }

    /**
     * @return Stream
     */
    public function postStream(array $values)
    {
        $db_service = Services::get('Database');

        $raw_values = array();
        $raw_values['name'] = $values['name'];

        if (!isset($values['auto_subscribe']))
        {
            $values['auto_subscribe'] = '1';
        }

        $raw_values['auto_subscribe'] = $values['auto_subscribe'] ? 1 : 0;

        $stream_id = $db_service->createTableRow('activity_streams', $raw_values);

        return $this->getStream($stream_id);
    }

}
