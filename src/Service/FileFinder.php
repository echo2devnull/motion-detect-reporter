<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;

class FileFinder
{
    const EXTENSION_MP4 = 'mp4';
    const VIDEO_LENGTH_MINUTES = 10;
    const FORMAT_DATE = 'Y-m-d';
    const FORMAT_DATE_TIME = 'Y-m-d H-i-s'; // strange motion naming

    public function find(string $basePath, \DateTime $from, \DateTime $to): array
    {
        if (!is_dir($basePath) || !is_readable($basePath)) {
            throw new \InvalidArgumentException('Invalid directory: '.$basePath);
        }

        $files = new \AppendIterator();
        /** @var \SplFileInfo $directory */
        foreach ($this->getIntervalCategories($basePath, $from, $to) as $directory) {
            $files->append($this->getIntervalFiles($directory, $from, $to)->getIterator());
        }

        return array_unique(iterator_to_array($files));
    }

    /**
     * @param string $basePath
     * @param \DateTime $from
     * @param \DateTime $to
     * @return Finder
     */
    private function getIntervalCategories(string $basePath, \DateTime $from, \DateTime $to): Finder
    {
        $finder = new Finder();
        $finder
            ->directories()
            ->in($basePath)
            ->filter(function (\SplFileInfo $dir) use ($from, $to) {
                $dirDate = \DateTime::createFromFormat(self::FORMAT_DATE, $dir->getBasename());

                return $dirDate->format(self::FORMAT_DATE) == $from->format(self::FORMAT_DATE)
                    || $dirDate->format(self::FORMAT_DATE) == $to->format(self::FORMAT_DATE);

            })
            ->sortByName();

        return $finder;
    }

    /**
     * @param \SplFileInfo $dir
     * @param \DateTime $from
     * @param \DateTime $to
     * @return Finder
     */
    private function getIntervalFiles(\SplFileInfo $dir, \DateTime $from, \DateTime $to): Finder
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($dir->getRealPath())
            ->name('*.'.self::EXTENSION_MP4)
            ->filter(function (\SplFileInfo $file) use ($from, $to, $dir) {
                $fileDateFrom = \DateTime::createFromFormat(
                    self::FORMAT_DATE_TIME,
                    $dir->getBasename().' '.$file->getBasename('.'. self::EXTENSION_MP4)
                );
                $fileDateTo = (clone $fileDateFrom)->modify(sprintf('+%d minutes', self::VIDEO_LENGTH_MINUTES));

                return ($fileDateFrom <= $from && $fileDateTo >= $to)
                    || ($fileDateFrom >= $from && $fileDateTo <= $to)
                    || ($fileDateFrom <= $to && $fileDateTo >= $to);
            })
            ->sortByName();

        return $finder;
    }
}
