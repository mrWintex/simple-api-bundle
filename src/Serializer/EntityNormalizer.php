<?php
namespace Wintex\SimpleApiBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EntityNormalizer implements NormalizerInterface
{
	public const EXTRACT_PROPERTY = "wsap_extract_property";
    private NormalizerInterface $normalizer;

	public function __construct(NormalizerInterface $normalizer) 
	{
		$this->normalizer = $normalizer;
	}

	public function normalize(mixed $object, string $format = null, array $context = array()) 
	{
		$object = $this->normalizer->normalize($object, $format, $context);
		$this->extractProperty($object, $context);
		return $object;
	}

	public function extractProperty(mixed &$object, array $context)
	{
		if (!isset($context[self::EXTRACT_PROPERTY]))
			return;

		$propObjects = $context[self::EXTRACT_PROPERTY];
	
		foreach ($propObjects as $propObject) {
			if (!isset($object[$propObject["object"]]))
				continue;

			if (!isset($object[$propObject["object"]][$propObject["property"]]))
				continue;

			$object[$propObject["object"]] = $object[$propObject["object"]][$propObject["property"]];
		}
	}

	public function supportsNormalization(mixed $data, string $format = null) 
	{
		return \is_object($data) && !$data instanceof \Traversable;
	}
}