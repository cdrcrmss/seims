

<?php $__env->startSection('title', 'New Reservation'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8" x-data="reservationForm()">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="<?php echo e(route('reservations.index')); ?>" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">New Reservation</h1>
            <p class="text-gray-600">Reserve equipment or rooms for your activities</p>
        </div>
    </div>

    <!-- Rate Limit / Global Errors -->
    <?php if($errors->has('rate_limit')): ?>
        <div class="max-w-4xl bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php echo e($errors->first('rate_limit')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->has('conflict')): ?>
        <div class="max-w-4xl bg-orange-50 ring-1 ring-orange-200 text-orange-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <?php echo e($errors->first('conflict')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->has('limit')): ?>
        <div class="max-w-4xl bg-yellow-50 ring-1 ring-yellow-200 text-yellow-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php echo e($errors->first('limit')); ?>

        </div>
    <?php endif; ?>

    <div class="max-w-4xl grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="<?php echo e(route('reservations.store')); ?>" @submit="handleSubmit($event)" class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 space-y-6 animate-fade-in-up stagger-1">
                <?php echo csrf_field(); ?>

                <!-- Reservation Type -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Reservation Type</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="reservation_type" value="equipment" x-model="type" class="peer hidden" required>
                            <div class="peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50 bg-gray-50 rounded-xl p-4 text-center transition-all hover:bg-gray-100">
                                <svg class="w-7 h-7 mx-auto mb-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="text-xs font-semibold">Equipment</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="reservation_type" value="room" x-model="type" class="peer hidden">
                            <div class="peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50 bg-gray-50 rounded-xl p-4 text-center transition-all hover:bg-gray-100">
                                <svg class="w-7 h-7 mx-auto mb-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="text-xs font-semibold">Room</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="reservation_type" value="both" x-model="type" class="peer hidden">
                            <div class="peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50 bg-gray-50 rounded-xl p-4 text-center transition-all hover:bg-gray-100">
                                <svg class="w-7 h-7 mx-auto mb-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                <span class="text-xs font-semibold">Both</span>
                            </div>
                        </label>
                    </div>
                    <?php $__errorArgs = ['reservation_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Equipment Selection -->
                <div x-show="type === 'equipment' || type === 'both'" x-transition>
                    <label for="item_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Equipment</label>
                    <select name="item_id" id="item_id" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                        <option value="">Select equipment...</option>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->id); ?>" <?php echo e(old('item_id') == $item->id ? 'selected' : ''); ?>>
                                <?php echo e($item->name); ?> (<?php echo e($item->available_stock); ?> available)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['item_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Room Selection -->
                <div x-show="type === 'room' || type === 'both'" x-transition>
                    <label for="room_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Room</label>
                    <select name="room_id" id="room_id" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                        <option value="">Select room...</option>
                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($room->id); ?>" <?php echo e(old('room_id') == $room->id ? 'selected' : ''); ?>>
                                <?php echo e($room->name); ?> — <?php echo e($room->building); ?>, <?php echo e($room->floor); ?> (Cap: <?php echo e($room->capacity); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Schedule -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_datetime" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start Date & Time</label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" x-model="startDatetime" value="<?php echo e(old('start_datetime')); ?>" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                        <?php $__errorArgs = ['start_datetime'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="end_datetime" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" x-model="endDatetime" value="<?php echo e(old('end_datetime')); ?>" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                        <?php $__errorArgs = ['end_datetime'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Duration indicator -->
                <div x-show="startDatetime && endDatetime" x-transition class="text-xs text-gray-500 -mt-2">
                    Duration: <span x-text="computeDuration()" class="font-medium text-gray-700"></span>
                    <span x-show="durationHours > 8" class="text-red-500 font-medium ml-1">(max 8 hours)</span>
                </div>

                <!-- Availability Check Result -->
                <div x-show="availabilityChecked" x-transition>
                    <div :class="available ? 'bg-green-50 ring-1 ring-green-200 text-green-700' : 'bg-red-50 ring-1 ring-red-200 text-red-700'" class="rounded-xl p-4 text-sm flex items-center space-x-3">
                        <template x-if="available">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </template>
                        <template x-if="!available">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </template>
                        <span x-text="available ? 'The selected time slot is available!' : 'Conflict detected! This resource is already reserved for the selected time.'"></span>
                    </div>
                </div>

                <!-- Check Availability Button -->
                <button type="button" @click="checkAvailability()" :disabled="!canCheckAvailability()" 
                        :class="canCheckAvailability() ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-gray-50 text-gray-400 cursor-not-allowed'"
                        class="w-full px-4 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Check Availability</span>
                </button>

                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Purpose <span class="font-normal normal-case text-gray-400">(min 10 characters)</span>
                    </label>
                    <textarea name="purpose" id="purpose" rows="3" x-model="purpose"
                              placeholder="Describe the purpose of your reservation in detail..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none" required><?php echo e(old('purpose')); ?></textarea>
                    <div class="flex justify-between mt-1">
                        <?php $__errorArgs = ['purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <span class="text-xs ml-auto" :class="purpose.length >= 10 ? 'text-green-600' : 'text-gray-400'" x-text="purpose.length + '/500'"></span>
                    </div>
                </div>

                <!-- Notes (optional) -->
                <div>
                    <label for="notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Additional Notes <span class="font-normal normal-case text-gray-400">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="2"
                              placeholder="Any special requirements or setup needs..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"><?php echo e(old('notes')); ?></textarea>
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="submitting || purpose.length < 10"
                            :class="(submitting || purpose.length < 10) ? 'bg-green-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 shadow-sm hover:shadow-md'"
                            class="flex-1 inline-flex items-center justify-center gap-2 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-200">
                        <template x-if="!submitting">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <template x-if="submitting">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </template>
                        <span x-text="submitting ? 'Submitting...' : 'Submit Reservation'"></span>
                    </button>
                    <a href="<?php echo e(route('reservations.index')); ?>" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-all duration-200">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-4 animate-fade-in-up stagger-2">
            <!-- Active Reservations -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Active Reservations</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-end justify-between mb-3">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-gray-900"><?php echo e($activeReservationCount); ?></span>
                            <span class="text-sm text-gray-400 font-medium">/ 5</span>
                        </div>
                        <span class="text-xs font-medium <?php echo e($activeReservationCount >= 5 ? 'text-red-500' : 'text-green-600'); ?>"><?php echo e(5 - $activeReservationCount); ?> slot<?php echo e((5 - $activeReservationCount) !== 1 ? 's' : ''); ?> remaining</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all <?php echo e($activeReservationCount >= 5 ? 'bg-red-500' : ($activeReservationCount >= 3 ? 'bg-amber-500' : 'bg-green-500')); ?>" style="width: <?php echo e(min(($activeReservationCount / 5) * 100, 100)); ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Guidelines</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-md bg-green-50 ring-1 ring-green-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-sm text-gray-700">Max 8 hours per session</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-md bg-green-50 ring-1 ring-green-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-sm text-gray-700">Book up to 30 days ahead</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-md bg-green-50 ring-1 ring-green-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-sm text-gray-700">Up to 5 active reservations</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-md bg-green-50 ring-1 ring-green-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-sm text-gray-700">Purpose min. 10 characters</span>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-md bg-amber-50 ring-1 ring-amber-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span class="text-sm text-gray-500">5 submissions per 5 min</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tip -->
            <div class="bg-blue-50 rounded-2xl ring-1 ring-blue-100 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-900 mb-1">Quick Tip</p>
                            <p class="text-sm text-blue-700 leading-relaxed">Use "Check Availability" before submitting to verify your time slot is free.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function reservationForm() {
    return {
        type: '<?php echo e(old("reservation_type", "equipment")); ?>',
        startDatetime: '<?php echo e(old("start_datetime", "")); ?>',
        endDatetime: '<?php echo e(old("end_datetime", "")); ?>',
        purpose: '<?php echo e(old("purpose", "")); ?>',
        available: false,
        availabilityChecked: false,
        submitting: false,
        durationHours: 0,

        canCheckAvailability() {
            return this.startDatetime && this.endDatetime && (this.type !== '');
        },

        computeDuration() {
            if (!this.startDatetime || !this.endDatetime) return '';
            const start = new Date(this.startDatetime);
            const end = new Date(this.endDatetime);
            const diffMs = end - start;
            if (diffMs <= 0) return 'Invalid';
            const hours = Math.floor(diffMs / 3600000);
            const mins = Math.floor((diffMs % 3600000) / 60000);
            this.durationHours = diffMs / 3600000;
            if (hours > 0 && mins > 0) return hours + 'h ' + mins + 'm';
            if (hours > 0) return hours + 'h';
            return mins + 'm';
        },

        handleSubmit(e) {
            if (this.submitting || this.purpose.length < 10) {
                e.preventDefault();
                return;
            }
            this.submitting = true;
        },

        async checkAvailability() {
            if (!this.canCheckAvailability()) return;
            const formData = new FormData();
            formData.append('start_datetime', this.startDatetime);
            formData.append('end_datetime', this.endDatetime);
            
            const itemSelect = document.getElementById('item_id');
            const roomSelect = document.getElementById('room_id');
            if (itemSelect && itemSelect.value) formData.append('item_id', itemSelect.value);
            if (roomSelect && roomSelect.value) formData.append('room_id', roomSelect.value);

            try {
                const resp = await fetch('<?php echo e(route("reservations.check-availability")); ?>', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await resp.json();
                this.available = data.available;
                this.availabilityChecked = true;
            } catch (e) {
                console.error('Availability check failed:', e);
            }
        }
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Cedric\SEIMS\resources\views/reservations/create.blade.php ENDPATH**/ ?>