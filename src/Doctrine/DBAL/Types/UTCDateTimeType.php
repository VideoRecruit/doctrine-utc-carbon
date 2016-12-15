<?php

namespace VideoRecruit\Doctrine\DBAL\Types;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use InvalidArgumentException;

/**
 * DateTime type which always store DateTime in UTC and returns DateTime as a Carbon instance.
 */
class UTCDateTimeType extends DateTimeType
{
	/**
	 * Converts the Datetime to UTC before storing to database
	 *
	 * @param mixed $value
	 * @param AbstractPlatform $platform
	 * @return mixed|NULL
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value instanceof DateTime) {
			$value->setTimezone(new DateTimeZone('UTC'));
		}

		return parent::convertToDatabaseValue($value, $platform);
	}

	/**
	 * Converts the Datetime from UTC to default timezone
	 *
	 * @param mixed $value
	 * @param AbstractPlatform $platform
	 * @return Carbon|NULL
	 * @throws ConversionException
	 * @throws InvalidArgumentException
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === NULL || $value instanceof Carbon) {
			return $value;
		}

		$converted = Carbon::createFromFormat(
			$platform->getDateTimeFormatString(),
			$value,
			new DateTimeZone('UTC')
		);
		$converted->setTimezone(new DateTimeZone(date_default_timezone_get()));

		if (!$converted) {
			throw ConversionException::conversionFailedFormat(
				$value,
				$this->getName(),
				$platform->getDateTimeFormatString()
			);
		}

		return $converted;
	}
}
