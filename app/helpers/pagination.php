<?php
/**
 * Pagination Helper
 */

class Paginator {
    public int $totalItems;
    public int $perPage;
    public int $currentPage;
    public int $totalPages;
    public int $offset;

    public function __construct(int $totalItems, int $perPage = 10, int $currentPage = 1) {
        $this->totalItems = max(0, $totalItems);
        $this->perPage = max(1, $perPage);
        $this->totalPages = (int)ceil($this->totalItems / $this->perPage) ?: 1;
        $this->currentPage = min(max(1, $currentPage), $this->totalPages);
        $this->offset = ($this->currentPage - 1) * $this->perPage;
    }

    public function hasPages(): bool {
        return $this->totalPages > 1;
    }

    public function hasPrev(): bool {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool {
        return $this->currentPage < $this->totalPages;
    }

    public function prevPage(): int {
        return max(1, $this->currentPage - 1);
    }

    public function nextPage(): int {
        return min($this->totalPages, $this->currentPage + 1);
    }
}
