# activitystreams-php

* Version: 1.0-dev
* Date: Not yet released
* Build Status: [![Build Status](https://secure.travis-ci.org/DracoBlue/activitystreams-php.png?branch=master)](http://travis-ci.org/DracoBlue/activitystreams-php), 100% Code Coverage

This is an activity stream server and client. It's intended to implement a RESTful service to create, publish, (un)subscribe activity streams (according to the "JSON Activity Streams 1.0"[1]).

  [1]: http://activitystrea.ms/specs/json/1.0/
  
## Terminology

A **Stream** contains multiple **Activities**. Those **Activities** are posted by **Objects**. An **Object** can subscribe/unsubscribe to/from multiple **Streams**. The **Object** can pull the most recent **Activities** (from the Streams he **subscribed** to) by fetching his **Feed**.

A **Stream** can be created with **auto_subscribe** attribute, which marks it as **subscribed** by all **Objects**. **Objects** are still able to **unsubscribe** these **Streams**.

## Example

### Creating a new Client

The endpoint for the client is here:

    $client = new AsClient('http://localhost/server/pub/index.php/');

### Create/Open an Application

Now you have to create your application:

    $application = $client->createApplication('my_app');
    
You should store the credentials for this application somewhere. You can
recieve the id by using `$application->getId()` and the secret by using
`$application->getSecret()`.

If you already have id and secret, you can get the application by using
the credentials.

    $application = $client->getApplicationByIdAndSecret($id, $secret);

### Create/Open a Stream

To create a new stream, choose if you want to make it public or private. A public stream will automatically be subscribed by all actors.

    // every actor subscribes this stream!
    $public_stream = $application->createStream("public_stream", true);
    // actors have to subscribe to this stream manually!
    $private_stream = $application->createStream("private_stream", false);

You can fetch a stream by id, if you created the stream earlier:

    $public_stream = $application->getStreamById("public_stream");

### Subscribe/Unsubscribe to/from a Stream

To create a new reader or writer for the stream, you have to create an object.
    
    $actor1 = $application->createObject('user1');

Now you can write with that object to any stream (it does not matter if the object subscribed to this stream or not).

    $public_stream->createActivity(array('title' => 'I posted a (public) new link', 'verb' => 'post'), $actor1);
    $private_stream->createActivity(array('title' => 'I posted a (private) new link', 'verb' => 'post'), $actor1);

For now the object would only get the data from the public_stream, so you have to subscribe to the private_stream to receive the activities from this stream, too.

    $client->subscribeObjectToStream($actor1, $private_stream);

Finally we can fetch the most recent posts of an actor (will be 2!).

    $activities = $client->getFeedForObject($actor1, 0, 20);
    assert(count($activities) == 2);

## TODO

* Implement the paper "Feeding Frenzy: Selectively Materializing Users’ Event Feeds" from <http://research.yahoo.com/pub/3203>
* Clean up the code
* Finish the tests
* Add database structure and way to create the database from scratch

## License

This work is copyright by DracoBlue (<http://dracoblue.net>) and licensed under the terms of MIT License.
