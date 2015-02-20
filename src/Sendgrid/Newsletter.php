<?php

namespace Sendgrid;

/**
 * All the methods returns an array of data or one string / int
 * If false returned you can run getLastResponseError() to see the error information
 * If error information == NULL then no error accrued (like deleting a record returns 0 records deleted if no record found)
 */
class Newsletter extends Core\Connect
{
    /**
     * Create a new Newsletter
     * @param $identity - The Identity that will be used for the Newsletter being created.
     * @param string $name - The name that will be used for the Newsletter being created.
     * @param $subject - The subject that will be used for the Newsletter being created.
     * @param $text - The text portion of the Newsletter being created.
     * @param $html - The html portion of the Newsletter being created.
     * @return array
     */
    public function add($identity, $name = 'Newsletter Using API', $subject, $text, $html)
    {
        $url = 'newsletter/add';

        $postData = array(
            'identity' => $identity,
            'name' => $name,
            'subject' => $subject,
            'text' => $text,
            'html' => $html
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Edit an existing Newsletter.
     * @param string $identity - The new Identity for the Newsletter being edited.
     * @param string $name - The name of the Newsletter being updated.
     * @param string $newname - The new name for the Newsletter being edited.
     * @param string $subject -The new subject that will be used for the Newsletter being edited.
     * @param string $text - The new text portion of the Newsletter being edited.
     * @param string $html - The new html portion of the Newsletter being edited.
     * @return array
     */
    public function edit($identity, $name, $newname = 'Newsletter Using API', $subject, $text, $html)
    {
        $url = 'newsletter/edit';

        $postData = array(
            'identity' => $identity,
            'name' => $name,
            'newname' => $newname,
            'subject' => $subject,
            'text' => $text,
            'html' => $html
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Retrieve the content of an existing Newsletter.
     * @param string $name - Must be an existing Newsletter.
     * @return array
     */
    public function get($name)
    {
        $url = 'newsletter/get';

        $postData = array(
            'name' => $name
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Remove the Newsletter with this name.
     * @param string $name - Must be an existing Newsletter.
     * @return array
     */
    public function delete($name)
    {
        $url = 'newsletter/delete';

        $postData = array(
            'name' => $name
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Retrieve a list of all existing Newsletter.
     * @param string $name - Can be an existing Newsletter.
     * @return array
     */
    public function getNewsletterList($name = '')
    {
        $url = 'newsletter/list';

        $postData = array(
            'name' => $name
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Create a new Recipient List.
     * @param string $list - Create a Recipient List with this name.
     * @param string $name - Specify the column name for the ‘name’ associated with email addresses.
     * @return array
     */
    public function addRecipientList($list, $name = '')
    {
        $url = 'newsletter/lists/add';

        $postData = array(
            'list' => $list,
            'name' => $name
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Edit an Existing Recipient List.
     * @param string $list - This is the name of the Recipient List to be renamed.
     * @param string $newlist - Specify the new name for the Recipient List.
     * @return array
     */
    public function editRecipientList($list, $newlist)
    {
        $url = 'newsletter/lists/edit';

        $postData = array(
            'list' => $list,
            'newlist' => $newlist
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Get an Existing Recipient List.
     * @param string $list - Check for this particular list. (To list all Recipient Lists on your account exclude this parameter)
     * @return array
     */
    public function getRecipientList($list = '')
    {
        $url = 'newsletter/lists/get';

        $postData = array(
            'list' => $list
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Remove this Recipient List.
     * @param string $list - Must be an existing Recipient List.
     * @return array
     */
    public function deleteRecipientList($list)
    {
        $url = 'newsletter/lists/delete';

        $postData = array(
            'list' => $list
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Add an email to an existing Recipient List.
     * @param string $list - The list which you are adding email addresses too.
     * @param array $data - Specify the name, email address, and additional fields to add to the specified Recipient List..
     *    EX: $data = array(
     *                'email'    =>    'test1@test.com',
     *                'name'    =>    'John Doe',
     *                'Address' => '1234 Cool St',
     *                'Zip Code' => '90210',
     *            );
     * must use email and name fields (other fileds are optional)
     * @return bool|int
     */
    public function addEmailToRecipientList($list, $data)
    {
        $url = 'newsletter/lists/email/add';

        $postData = array(
            'list' => $list,
            'data' => json_encode($data)
        );

        $results = $this->makeApiCall($url, $postData);

        return array_key_exists('inserted', $results) ? $results['inserted'] : false;
    }

    /**
     * Add emails to an existing Recipient List. Max 1000 per request
     * @param $list
     * @param array $recipients
     * @return bool|int
     */
    public function addEmailsToRecipientList($list, array $recipients)
    {
        $url = 'newsletter/lists/email/add';

        $postData = array(
            'list' => $list,
            'data' => array_map(function($recipient) {
                return json_encode($recipient);
            }, $recipients)
        );

        $results = $this->makeApiCall($url, $postData);

        return array_key_exists('inserted', $results) ? $results['inserted'] : false;
    }

    /**
     * Edit an email of an existing Recipient List. (Not Supported by SENDGRID)
     * @param string $list - The list which you are editing the contact
     * @param string $email - The Contact which you are editing
     * @param array $data - Specify the name, email address, and additional fields to add to the specified Recipient List..
     *    EX: $data = array(
     *                'email'    =>    'test1@test.com',
     *                'name'    =>    'John Doe',
     *                'Address' => '1234 Cool St',
     *                'Zip Code' => '90210',
     *            );
     * must use email and name fields (other fileds are optional)
     * @return bool|int
     */
    public function editRecipientListEmail($list, $email, $data)
    {
        $originalContact = $this->getRecipientListEmailData($list, $email);

        if (!$originalContact) {
            return false;
        } // if the current contact not exist nothing to edit

        $this->newsletter_lists_email_delete($list, $email); // deleteing the current contact

        if (!$this->addEmailToRecipientList($list, $data)) { // adding the new contact information
            $this->addEmailToRecipientList($list, $originalContact[0]); // if failes reEntering the old contact back
            return false;
        }
        return 1;
    }

    /**
     * Get the email addresses and associated fields for a Recipient List.
     * @param string $list - The list you are searching.
     * @param string $email - Optional email addresses to search for in the Recipient List.
     * @return array
     */
    public function getRecipientListEmailData($list, $email = '')
    {
        $url = 'newsletter/lists/email/get';

        $postData = array(
            'list' => $list,
            'email' => $email
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Remove one email from a Recipient List.
     * @param string $list - The list which you are removing email addresses from.
     * @param string $email - Specify the email address or email addresses you wish to remove from the specified Recipient List.
     * @return bool|int
     */
    public function deleteEmailFromRecipientList($list, $email)
    {
        $url = 'newsletter/lists/email/delete';

        $postData = array(
            'list' => $list,
            'email' => $email
        );

        $results = $this->makeApiCall($url, $postData);

        return array_key_exists('removed', $results) ? $results['removed'] : false;
    }

    /**
     * Create a new Identity.
     * @param string $identity - Create an Identity named this.
     * @param string $name - Specify the name to be used for this Identity.
     * @param string $email - Specify the email address to be used for this Identity.
     * @param string $address - Create an Identity named this.
     * @param string $city - Specify the city to be used for this Identity.
     * @param string $state - Specify the state code to be used for this Identity.
     * @param string $zip - Specify the zip code to be used for this Identity.
     * @param string $country - Specify the country code to be used for this Identity.
     * @return array
     */
    public function addIdentity($identity, $name, $email, $address, $city, $state, $zip, $country)
    {
        $url = 'newsletter/identity/add';

        $postData = array(
            'identity' => $identity,
            'name' => $name,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'country' => $country
        );

        return $this->makeApiCall($url, $postData);
    }


    /**
     * Edit an existing Identity.
     * @param string $identity - The Identity you wish to edit.
     * @param string $newidentity - Specify the new name to be used for this Identity.
     * @param string $name - Specify the name to be used for this Identity.
     * @param string $email - Specify the email address to be used for this Identity.
     * @param string $address - Create an Identity named this.
     * @param string $city - Specify the city to be used for this Identity.
     * @param string $state - Specify the state code to be used for this Identity.
     * @param string $zip - Specify the zip code to be used for this Identity.
     * @param string $country - Specify the country code to be used for this Identity.
     * @return array
     */
    public function editIdentity(
        $identity,
        $newidentity,
        $name,
        $email,
        $address,
        $city,
        $state,
        $zip,
        $country
    ) {
        $url = 'newsletter/identity/edit';

        $postData = array(
            'identity' => $identity,
            'newidentity' => $newidentity,
            'name' => $name,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'country' => $country
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Retrieve information associated with a particular Identity.
     * @param string $identity - Retrieve contents of the specified Identity.
     * @return array
     */
    public function getIdentity($identity)
    {
        $url = 'newsletter/identity/get';

        $postData = array(
            'identity' => $identity
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * List all Identities on your account, or check if a particular Identity exists.
     * @param string $identity - Check for this particular Identity. (To list all Identities on your account exclude this parameter)
     * @return array
     */
    public function getIdentityList($identity = '')
    {
        $url = 'newsletter/identity/list';

        $postData = array(
            'identity' => $identity
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Remove an Identity from your account.
     * @param string $identity - Remove the specified Identity from your account.
     * @return array
     */
    public function deleteIdentity($identity)
    {
        $url = 'newsletter/identity/delete';

        $postData = array(
            'identity' => $identity
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Add one or more Recipient List to a Newsletter.
     * @param string $name - This is the Newsletter to which you are adding Recipients Lists.
     * @param string $list - This is the Recipient List that will be added to the Newsletter.
     * @return array
     */
    public function addRecipients($name, $list)
    {
        $url = 'newsletter/recipients/add';

        $postData = array(
            'name' => $name,
            'list' => $list
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Retrieve the Recipient Lists attached to an existing Newsletter.
     * @param string $name - Retrieve the Recipient Lists of an existing Newsletter.
     * @return array
     */
    public function getRecipients($name)
    {
        $url = 'newsletter/recipients/get';

        $postData = array(
            'name' => $name
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Remove assigned recipient lists from marketing email.
     * @param string $name - Newsletter to remove Recipient Lists from.
     * @param string $list - Recipient Lists to remove.
     * @return array
     */
    public function deleteRecipients($name, $list)
    {
        $url = 'newsletter/recipients/delete';

        $postData = array(
            'name' => $name,
            'list' => $list
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Create a new schedule event.
     * @param string $name - Newsletter to schedule delivery for. (If Newsletter should be sent now, include no additional parameters.)
     * @param string $at - Date / Time to schedule newsletter Delivery.
     *    Date / Time must be provided in ISO 8601 format (YYYY-MM-DD HH:MM:SS +-HH:MM)
     * @param string $after - Number of minutes until delivery should occur. Must be a positive integer.
     * @return array
     */
    public function addSchedule($name, $at = '', $after = '')
    {
        $url = 'newsletter/schedule/add';

        $postData = array(
            'name' => $name,
            'at' => $at,
            'after' => $after
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Retrieve the scheduled delivery time for an existing Newsletter.
     * @param string $name - Retrieve the delivery time scheduled for this Newsletter.
     * @return array
     */
    public function getSchedule($name)
    {
        $url = 'newsletter/schedule/get';

        $postData = array(
            'name' => $name
        );

        return $this->makeApiCall($url, $postData);
    }

    /**
     * Cancel a scheduled send for a Newsletter.
     * @param string $name - Remove the scheduled delivery time from an existing Newsletter.
     * @return array
     */
    public function deleteSchedule($name)
    {
        $url = 'newsletter/schedule/delete';

        $postData = array(
            'name' => $name
        );

        return $this->makeApiCall($url, $postData);
    }
}
