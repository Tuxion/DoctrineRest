<?php namespace Tuxion\DoctrineRest\Domain\Result;

/**
 * Represents an unsuccessful find result in the domain.
 * It's body will be the parameters that led to this error.
 */
class NotFoundResult extends AbstractResult{}