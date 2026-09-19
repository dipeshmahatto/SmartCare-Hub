<?php
const APPOINTMENT_PENDING = 'pending';
const APPOINTMENT_CONFIRMED = 'confirmed';
const APPOINTMENT_COMPLETED = 'completed';
const APPOINTMENT_CANCELLED = 'cancelled';

function appointmentStatusMeta($status)
{
    $status = strtolower(trim((string) $status));
    $map = [
        APPOINTMENT_PENDING => [
            'label' => 'Pending',
            'class' => 'status-pending',
            'icon' => 'fa-regular fa-clock',
            'description' => 'Waiting for doctor confirmation',
        ],
        APPOINTMENT_CONFIRMED => [
            'label' => 'Confirmed',
            'class' => 'status-confirmed',
            'icon' => 'fa-solid fa-circle-check',
            'description' => 'Confirmed by the doctor',
        ],
        APPOINTMENT_COMPLETED => [
            'label' => 'Completed',
            'class' => 'status-completed',
            'icon' => 'fa-solid fa-check',
            'description' => 'Visit completed',
        ],
        APPOINTMENT_CANCELLED => [
            'label' => 'Cancelled',
            'class' => 'status-cancelled',
            'icon' => 'fa-solid fa-ban',
            'description' => 'Appointment cancelled',
        ],
    ];

    return $map[$status] ?? $map[APPOINTMENT_PENDING];
}

function appointmentIsActive($status)
{
    return in_array(strtolower((string) $status), [APPOINTMENT_PENDING, APPOINTMENT_CONFIRMED], true);
}
