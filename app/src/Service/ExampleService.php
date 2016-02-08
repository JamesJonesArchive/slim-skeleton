<?php
/**
 * Example Service
 *
 * @category epierce
 * @package slim-skeleton
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.opensource.org/licenses/MIT MIT
 * @link https://github.com/epierce/slim-skeleton
 */
namespace USF\IdM\PeopleSoftAuthenticator\Service;

use USF\IdM\UsfEncryption;
use USF\IdM\CLients\NamsIdentifierConversionClient;
use Psr\Log\LoggerInterface;
use Slim\Collection;
use League\Uri\Schemes\Http as HttpUri;

/**
 * Identify users and generate authentication tokens for PeopleSoftAuthenticator
 *
 * @category USF/IT
 * @package PeopleSoftAuthenticator
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache2.0
 * @link https://github.com/USF-IT/PeopleSoftAuthenticator
 */
class PeopleSoftService
{
    private $logger;
    private $client;
    private $settings;

    public function __construct(LoggerInterface $logger, Collection $settings )
    {
        $this->logger = $logger;
        $this->settings = $settings;

        $nams_config = $settings['nams'];
        $this->client = new NamsIdentifierConversionClient($nams_config['restCasClient']);
        $this->client->setNamsHost($nams_config['host']);
    }


    /**
     * Get list of usernames for this user from NAMS
     *
     * @param string $application PeopleSoft Application
     * @param string $netid       USF NetID
     * @return array
     * @throws \Exception
     */
    public function getUsernameList($application, $netid)
    {
        $results = $this->client->convertIdentifier('netid', 'accounts', $netid);

        $this->logger->debug("ws_convert result: ". json_encode($results));

        $hostType = $this->settings['accountTypes'][$application];

        $accountList = [];

        if (isset($results[$hostType])) {
            foreach ($results[$hostType] as $entry) {
                $accountList[] = $entry['username'];
            }
        }

        return $accountList;

    }

    /**
     * Verify that a given username is in the list of accounts owned by this user
     *
     * @param string $application      PeopleSoft Application
     * @param string $netid            USF NetID
     * @param string $selectedUsername Check for this username
     * @return bool
     */
    public function verifyAccountOwnership($application, $netid, $selectedUsername)
    {
        $psUsernameList = $this->getUsernameList($application, $netid);

        return in_array($selectedUsername, $psUsernameList);
    }

    /**
     * Generate an authentication URL for a PeopleSoft Application
     *
     * @param string $application      PeopleSoft Application
     * @param string $instance         PeopleSoft instance
     * @param string $netid            USF NetID
     * @param string $ipAddress        Client IP
     * @param string $deepLink         Page within People application to send the user to
     * @param string $selectedUsername Use this username for the service
     * @return string
     * @throws MultipleAccountsFoundException
     * @throws NoAccountFoundException
     * @throws \Exception
     */
    public function getRedirectUrl($application, $instance, $netid, $ipAddress, $deepLink = '', $selectedUsername = '')
    {
        $key = $this->settings['instances'][$instance][$application]['encryptionKey'];
        $blockType = $this->settings['instances'][$instance][$application]['encryptionBlockType'];

        if ($selectedUsername != '' && $this->verifyAccountOwnership($application, $netid, $selectedUsername)) {
            $psUsernameList = [$selectedUsername];
        } else {
            $psUsernameList = $this->getUsernameList($application, $netid);
        }

        if (count($psUsernameList) == 0) {
            throw new NoAccountFoundException("No accounts were found in ${application} (${instance}) for ${netid}");
        } elseif (count($psUsernameList) > 1) {
            $allowMulti = $this->settings['instances'][$instance][$application]['allowMultipleAccounts'] ?? false;
            if ($allowMulti) {
                return $psUsernameList;
            }
            throw new MultipleAccountsFoundException("Multiple accounts were found in ${application} (${instance}) for ${netid}: " . json_encode($psUsernameList));
        } else {
            $psUsername = $psUsernameList[0];
        }

        $plainToken = time()."|${application}|${ipAddress}|${psUsername}";
        $encryptedToken = urlencode(UsfEncryption::encrypt($key, $plainToken, $blockType));
        $this->logger->debug("Token plaintext: ${plainToken} | Final: ${encryptedToken}");

        $psPath = $this->settings['instances'][$instance][$application]['appPath'];
        $psQuery = ['cmd' => 'start', 'user' => $psUsername, 'app' => $application, 'token' => $encryptedToken];
        if ($deepLink !== '') {
            unset($psQuery['cmd']);
            $psQuery['NAVSTACK'] = 'Clear';
            $psPath = $psPath . $this->settings['instances'][$instance][$application]['deepLinkPath'] . $deepLink;
        }

        $url = HttpUri::createFromString('');
        $url = $url->withHost($this->settings['instances'][$instance][$application]['host'])
                   ->withScheme('https')
                   ->withPort($this->settings['instances'][$instance][$application]['port'])
                   ->withPath($psPath)
                   ->withQuery((string) \League\Uri\Components\Query::createFromArray($psQuery));

        return (string) $url;
    }
}

class NoAccountFoundException extends \Exception { }

class MultipleAccountsFoundException extends \Exception { }
