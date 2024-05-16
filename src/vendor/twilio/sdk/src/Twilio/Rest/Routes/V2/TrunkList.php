<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Routes\V2;

use Twilio\ListResource;
use Twilio\Version;

class TrunkList extends ListResource {
    /**
     * Construct the TrunkList
     *
     * @param Version $version Version that contains the resource
     */
    public function __construct(Version $version) {
        parent::__construct($version);

        // Path Solution
        $this->solution = [];
    }

    /**
     * Constructs a TrunkContext
     *
     * @param string $sipTrunkDomain The SIP Trunk
     */
    public function getContext(string $sipTrunkDomain): TrunkContext {
        return new TrunkContext($this->version, $sipTrunkDomain);
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        return '[Twilio.Routes.V2.TrunkList]';
    }
}