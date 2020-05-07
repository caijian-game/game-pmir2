<?php

namespace ZipkinOpenTracing;

use OpenTracing\Span as OTSpan;
use OpenTracing\SpanContext as OTSpanContext;
use Zipkin\Span as ZipkinSpan;
use ZipkinOpenTracing\SpanContext as ZipkinOpenTracingContext;

final class NoopSpan implements OTSpan
{
    /**
     * @var OTSpanContext|SpanContext
     */
    private $context;

    private function __construct(ZipkinSpan $span)
    {
        $this->context = ZipkinOpenTracingContext::fromTraceContext($span->getContext());
    }

    /**
     * @param ZipkinSpan $span
     * @return NoopSpan
     */
    public static function create(ZipkinSpan $span)
    {
        return new self($span);
    }

    /**
     * @return string
     */
    public function getOperationName()
    {
        return '';
    }

    /**
     * Yields the SpanContext for this Span. Note that the return value of
     * Span::getContext() is still valid after a call to Span::finish(), as is
     * a call to Span::getContext() after a call to Span::finish().
     *
     * @return SpanContext
     */
    public function getContext()
    {
        return NoopSpanContext::create($this->context->getContext());
    }

    /**
     * Sets the end timestamp and finalizes Span state.
     *
     * With the exception of calls to Context() (which are always allowed),
     * finish() must be the last call made to any span instance, and to do
     * otherwise leads to undefined behavior
     *
     * If the span is already finished, a warning should be logged.
     *
     * @param float|int|\DateTimeInterface|null $finishTime if passing float or int
     * it should represent the timestamp (including as many decimal places as you need)
     * @param array $logRecords
     */
    public function finish($finishTime = null, array $logRecords = [])
    {
    }

    /**
     * If the span is already finished, a warning should be logged.
     *
     * @param string $newOperationName
     */
    public function overwriteOperationName($newOperationName)
    {
    }

    /**
     * Sets tags to the Span in key:value format, key must be a string and tag must be either
     * a string, a boolean value, or a numeric type.
     *
     * As an implementor, consider using "standard tags" listed in {@see \OpenTracing\Ext\Tags}
     *
     * If the span is already finished, a warning should be logged.
     *
     * @param string $key
     * @param string $value
     */
    public function setTag($key, $value)
    {
    }

    /**
     * Adds a log record to the span in key:value format, key must be a string and tag must be either
     * a string, a boolean value, or a numeric type.
     *
     * If the span is already finished, a warning should be logged.
     *
     * @param array $fields
     * @param int|float|\DateTimeInterface $timestamp
     */
    public function log(array $fields = [], $timestamp = null)
    {
    }

    /**
     * Adds a baggage item to the SpanContext which is immutable so it is required to use
     * SpanContext::withBaggageItem to get a new one.
     *
     * If the span is already finished, a warning should be logged.
     *
     * @param string $key
     * @param string $value
     */
    public function addBaggageItem($key, $value)
    {
    }

    /**
     * @param string $key
     * @return string
     */
    public function getBaggageItem($key)
    {
        return '';
    }
}
