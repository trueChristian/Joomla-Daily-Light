<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.net>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/
namespace TrueChristianBible\Module\DailyLight\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class DailyLightHelper
{
	/**
	 * Params
	 *
	 * @var   Registry
	 * @since  1.0
	 */
	protected Registry $params;

	/**
	 * Daily Light
	 *
	 * @var   mixed
	 * @since  1.0
	 */
	protected $daily = null;

	/**
	 * Telegram Scripture
	 *
	 * @var   array
	 * @since  1.1
	 */
	protected array $telegram = [];

	/**
	 * Telegram Comments
	 *
	 * @var   array
	 * @since  1.1
	 */
	protected array $comments = [];

	/**
	 * Type
	 *
	 * @var   int
	 * @since  1.0
	 */
	protected int $type;

	/**
	 * Time of Day
	 *
	 * @var   int
	 * @since  1.0
	 */
	protected int $time;

	/**
	 * The Telegram Post evening ID
	 *
	 * @var   int
	 * @since  1.1
	 */
	protected int $evening = 0;

	/**
	 * The Telegram Post morning ID
	 *
	 * @var   int
	 * @since  1.1
	 */
	protected int $morning = 0;

	/**
	 * Constructor.
	 *
	 * @param   Registry|null  $params  the module settings
	 *
	 * @since   1.0
	 */
	public function __construct(Registry $params = null)
	{
		// we must have the params or we cant continue
		if ($params)
		{
			// set the global params
			$this->params = $params;
			// get the type
			$this->type = $params->get('type', 1);

			// implementation type = 1 = gitHub
			if ($this->type == 1)
			{
				// the link to the scripture for the day
				$path = $params->get('url_json', "https://raw.githubusercontent.com/trueChristian/daily-light/master/README.json");

				// get the daily light object
				$this->daily = $this->getFileContents($path);
			}
		}
	}

	/**
	 * get the Telegram Post Evening ID
	 *
	 * @param   string  $time  The time of daily light
	 *
	 * @return  int
	 * @since   1.1
	 */
	protected function getId($time): int
	{
		if ($this->$time == 0)
		{
			// the link to the daily light morning/evening
			$path = $this->params->get("url_{$time}_telegram_id", "https://raw.githubusercontent.com/trueChristian/daily-light/master/{$time}.tg.id");

			// get the scripture object
			$id = trim($this->getFileContents($path, false));

			// make sure we have a number here
			if (is_numeric($id))
			{
				$this->$time = (int) $id;
			}
		}

		return $this->$time;
	}

	/**
	 * Waco method to get an scripture value
	 *
	 * @param   mixed  $name  Name of the value to retrieve.
	 *
	 * @return  mixed  The request value
	 * @since   1.0
	 */
	public function __get($key)
	{
		if ($this->type == 1 && $this->checkDaily($key))
		{
			return $this->daily->{$key};
		}
		elseif ($this->type == 2 && ($key === 'telegram.evening' || $key === 'telegram.morning'))
		{
			if ($key === 'telegram.evening')
			{
				return $this->getTelegram('evening');
			}
			elseif ($key === 'telegram.morning')
			{
				return $this->getTelegram('morning');
			}
		}
		elseif ($key === 'comments.evening' || $key === 'comments.morning')
		{
			if ($key === 'comments.evening')
			{
				return $this->getComments('evening');
			}
			elseif ($key === 'comments.morning')
			{
				return $this->getComments('morning');
			}
		}

		return null;
	}

	/**
	 * get the Telegram script
	 *
	 * @param   string  $time  The time of daily light
	 *
	 * @return  string|null
	 * @since   1.1
	 */
	protected function getTelegram(string $time): ?string
	{
		if (empty($this->telegram[$time]))
		{
			$this->setTelegram($time);
		}

		return (isset($this->telegram[$time]) &&
			$this->checkString($this->telegram[$time])) ? $this->telegram[$time] : null;
	}

	/**
	 * set the Telegram script
	 *
	 * @param   string  $time  The time of daily light
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected function setTelegram($time)
	{
		// validate the ID
		if (($id = $this->getId($time)) > 0)
		{
			// get the color
			$color = $this->getColor();

			// get the dark theme
			$dark = $this->getDarkTheme();

			// get the width
			$width = $this->params->get('width', 100);

			// get the userpic
			$userpic = $this->getUserPic();

			// set the daily light
			$this->telegram[$time] = "<script async src=\"https://telegram.org/js/telegram-widget.js?22\" data-telegram-post=\"daily_light/$id\" data-width=\"$width%\"{$color}{$userpic}{$dark}></script>";
		}
	}

	/**
	 * get the Telegram Comment script
	 *
	 * @param   string  $time  The time of daily light
	 *
	 * @return  string|null
	 * @since   1.1
	 */
	protected function getComments($time): ?string
	{
		if (empty($this->comments[$time]))
		{
			$this->setComments($time);
		}

		return (isset($this->comments[$time]) &&
			$this->checkString($this->comments[$time])) ? $this->comments[$time] : null;
	}

	/**
	 * set the Telegram script
	 *
	 * @param   string  $time  The time of daily light
	 *
	 * @return  void
	 * @since   1.1
	 */
	protected function setComments($time)
	{
		// should we add comments
		if ($this->params->get('show_comments', 0) == 1 && ($id = $this->getId($time)) > 0)
		{
			// get the color
			$color = $this->getColor();

			// get the dark theme
			$dark = $this->getDarkTheme();

			// get comment limit
			$limit = $this->params->get('comments_limit', 5);

			// get comment Height
			$height = $this->getCommentHeight();

			// get color ful switch
			$colorful = $this->getCommentColorful();

			// set the script
			$this->comments[$time] = "<script async src=\"https://telegram.org/js/telegram-widget.js?22\" data-telegram-discussion=\"daily_light/$id\" data-comments-limit=\"$limit\"{$colorful}{$height}{$color}{$dark}></script>";
		}
	}

	/**
	 * get the color
	 *
	 * @return  string  The telegram script
	 * @since   1.0
	 */
	protected function getColor()
	{
		// get the color
		$color = $this->params->get('color', 1);

		// convert to color
		switch($color)
		{
			case 2:
				// Cyan
				$color = '13B4C6';
				$dark_color = '39C4E8';
			break;
			case 3:
				// Green
				$color = '29B127';
				$dark_color = '72E350';
			break;
			case 4:
				// Yellow
				$color = 'CA9C0E';
				$dark_color = 'F0B138';
			break;
			case 5:
				// Red
				$color = 'E22F38';
				$dark_color = 'F95C54';
			break;
			case 6:
				// White
				$color = '343638';
				$dark_color = 'FFFFFF';
			break;
			case 7:
				// custom color
				$color = strtoupper(trim($this->params->get('custom_color', 'F646A4'), '#'));
				$dark_color = null;
			break;
			default:
				// default
				$color = null;
				$dark_color = null;
			break;
		}

		// load colors if set
		if ($color)
		{
			$color = " data-color=\"$color\"";
			// load dark color if set
			if ($dark_color)
			{
				$color = "$color data-dark-color=\"$dark_color\"";
			}
			$this->color = $color;
		}
	}

	/**
	 * get the dark theme state
	 *
	 * @return  string data-dark value
	 * @since   1.0
	 */
	protected function getDarkTheme()
	{
		// get the theme
		$theme = $this->params->get('theme', 1);

		// only load if dark theme is set
		if ($theme == 2)
		{
			$this->dark =  " data-dark=\"1\"";
		}
	}

	/**
	 * get the comment height
	 *
	 * @return  string height value
	 * @since   1.1
	 */
	protected function getCommentHeight()
	{
		if (($height = $this->params->get('comments_height')) > 300)
		{
			return " data-height=\"$height\"";
		}

		return '';
	}

	/**
	 * get the comment color ful switch
	 *
	 * @return  string height value
	 * @since   1.1
	 */
	protected function getCommentColorful()
	{
		if (($colorful = $this->params->get('comments_colorful', 0)) == 1)
		{
			return " data-colorful=\"1\"";
		}
		return '';
	}

	/**
	 * get the user pic state
	 *
	 * @return  string data-userpic value
	 * @since   1.0
	 */
	protected function getUserPic()
	{
		// get the author_photo
		$author_photo = $this->params->get('author_photo', 1);
		// convert to userpic
		switch($author_photo)
		{
			case 2:
				// Always show
				$userpic = 'true';
			break;
			case 3:
				// Always hide
				$userpic = 'false';
			break;
			default:
				// Auto
				$userpic = null;
			break;
		}
		// load userpic if set
		if ($userpic)
		{
			$userpic = " data-userpic=\"$userpic\"";
			return $userpic;
		}
		return '';
	}

	/**
	 * get the file content
	 *
	 * @input	string $path   The path to get remotely 
	 *
	 * @returns mixed on success
	 * @since  1.0
	 */
	protected function getFileContents($path, $json = true)
	{
		// use basic file get content for now
		if (($content = @file_get_contents($path)) !== FALSE)
		{
			// return if found
			if ($json)
			{
				if ($this->checkJson($content))
				{
					return json_decode($content);
				}
			}
			elseif ($this->checkString($content))
			{
				return $content;
			}
		}
		// use curl if available
		elseif (function_exists('curl_version'))
		{
			// start curl
			$ch = curl_init();
			// set the options
			$options = array();
			$options[CURLOPT_URL] = $path;
			$options[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12';
			$options[CURLOPT_RETURNTRANSFER] = TRUE;
			$options[CURLOPT_SSL_VERIFYPEER] = FALSE;
			// load the options
			curl_setopt_array($ch, $options);
			// get the content
			$content = curl_exec($ch);
			// close the connection
			curl_close($ch);
			// return if found
			if ($json)
			{
				if ($this->checkJson($content))
				{
					return json_decode($content);
				}
			}
			elseif ($this->checkString($content))
			{
				return $content;
			}
		}

		return null;
	}

	/**
	 * Check if have an json string
	 *
	 * @input	string   The json string to check
	 *
	 * @returns bool true on success
	 * @since  1.0
	 */
	protected function checkJson($string)
	{
		if ($this->checkString($string))
		{
			json_decode($string);
			return (json_last_error() === JSON_ERROR_NONE);
		}
		return false;
	}

	/**
	 * Check if have a string with a length
	 *
	 * @input	string   The string to check
	 *
	 * @returns bool true on success
	 * @since  1.0
	 */
	protected function checkString($string)
	{
		if (isset($string) && is_string($string) && strlen($string) > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if we have an daily light object with value
	 *
	 * @input	key   The key being requested
	 *
	 * @returns bool true on success
	 * @since  1.0
	 */
	protected function checkDaily($key)
	{
		if (isset($this->daily) && is_object($this->daily) && isset($this->daily->{$key}))
		{
			return true;
		}
		return false;
	}

}
