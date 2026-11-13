<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";

$demoPassword = "DemoPassword123!";
$summary = [
    "users" => ["inserted" => 0, "skipped" => 0],
    "donors" => ["inserted" => 0, "skipped" => 0],
    "recipients" => ["inserted" => 0, "skipped" => 0],
    "matches" => ["inserted" => 0, "skipped" => 0],
    "notifications" => ["inserted" => 0, "skipped" => 0]
];

$users = [
    ["name" => "Demo Administrator", "email" => "demo.admin@example.test", "role" => "admin"],
    ["name" => "Demo Donor One", "email" => "demo.donor.one@example.test", "role" => "donor"],
    ["name" => "Demo Donor Two", "email" => "demo.donor.two@example.test", "role" => "donor"],
    ["name" => "Demo Donor Three", "email" => "demo.donor.three@example.test", "role" => "donor"],
    ["name" => "Demo Recipient One", "email" => "demo.recipient.one@example.test", "role" => "recipient"],
    ["name" => "Demo Recipient Two", "email" => "demo.recipient.two@example.test", "role" => "recipient"],
    ["name" => "Demo Recipient Three", "email" => "demo.recipient.three@example.test", "role" => "recipient"]
];

$donorProfiles = [
    ["email" => "demo.donor.one@example.test", "age" => 29, "blood_type" => "O+", "organ" => "kidney", "availability" => "available", "phone" => "5551000001"],
    ["email" => "demo.donor.two@example.test", "age" => 41, "blood_type" => "A+", "organ" => "liver", "availability" => "available", "phone" => "5551000002"],
    ["email" => "demo.donor.three@example.test", "age" => 34, "blood_type" => "B+", "organ" => "heart", "availability" => "available", "phone" => "5551000003"]
];

$recipientProfiles = [
    ["email" => "demo.recipient.one@example.test", "age" => 38, "blood_type" => "O+", "organ_needed" => "kidney", "urgency" => "high", "hospital" => "Demo General Hospital"],
    ["email" => "demo.recipient.two@example.test", "age" => 52, "blood_type" => "A+", "organ_needed" => "liver", "urgency" => "critical", "hospital" => "Demo City Hospital"],
    ["email" => "demo.recipient.three@example.test", "age" => 46, "blood_type" => "B+", "organ_needed" => "heart", "urgency" => "medium", "hospital" => "Demo Regional Hospital"]
];

function report(string $type, string $action, string $detail): void
{
    echo strtoupper($type) . " - " . $action . ": " . $detail . PHP_EOL;
}

try {
    $pdo->beginTransaction();

    $userIds = [];
    $findUser = $pdo->prepare("SELECT id, role FROM users WHERE email = ? LIMIT 1");
    $insertUser = $pdo->prepare(
        "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)"
    );

    foreach ($users as $user) {
        $findUser->execute([$user["email"]]);
        $existing = $findUser->fetch();

        if ($existing) {
            if ($existing["role"] !== $user["role"]) {
                throw new RuntimeException("Existing demo email has an unexpected role: " . $user["email"]);
            }
            $userIds[$user["email"]] = (int) $existing["id"];
            $summary["users"]["skipped"]++;
            report("users", "SKIPPED", $user["email"] . " already exists");
            continue;
        }

        $insertUser->execute([
            $user["name"],
            $user["email"],
            password_hash($demoPassword, PASSWORD_DEFAULT),
            $user["role"]
        ]);
        $userIds[$user["email"]] = (int) $pdo->lastInsertId();
        $summary["users"]["inserted"]++;
        report("users", "INSERTED", $user["email"]);
    }

    $donorIds = [];
    $findDonor = $pdo->prepare("SELECT id FROM donors WHERE user_id = ? LIMIT 1");
    $insertDonor = $pdo->prepare(
        "INSERT INTO donors (user_id, age, blood_type, organ, availability, phone)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    foreach ($donorProfiles as $profile) {
        $userId = $userIds[$profile["email"]];
        $findDonor->execute([$userId]);
        $existing = $findDonor->fetch();

        if ($existing) {
            $donorIds[$profile["email"]] = (int) $existing["id"];
            $summary["donors"]["skipped"]++;
            report("donors", "SKIPPED", $profile["email"] . " already has a profile");
            continue;
        }

        $insertDonor->execute([
            $userId,
            $profile["age"],
            $profile["blood_type"],
            $profile["organ"],
            $profile["availability"],
            $profile["phone"]
        ]);
        $donorIds[$profile["email"]] = (int) $pdo->lastInsertId();
        $summary["donors"]["inserted"]++;
        report("donors", "INSERTED", $profile["email"]);
    }

    $recipientIds = [];
    $findRecipient = $pdo->prepare("SELECT id FROM recipients WHERE user_id = ? LIMIT 1");
    $insertRecipient = $pdo->prepare(
        "INSERT INTO recipients (user_id, age, blood_type, organ_needed, urgency, hospital)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    foreach ($recipientProfiles as $profile) {
        $userId = $userIds[$profile["email"]];
        $findRecipient->execute([$userId]);
        $existing = $findRecipient->fetch();

        if ($existing) {
            $recipientIds[$profile["email"]] = (int) $existing["id"];
            $summary["recipients"]["skipped"]++;
            report("recipients", "SKIPPED", $profile["email"] . " already has a profile");
            continue;
        }

        $insertRecipient->execute([
            $userId,
            $profile["age"],
            $profile["blood_type"],
            $profile["organ_needed"],
            $profile["urgency"],
            $profile["hospital"]
        ]);
        $recipientIds[$profile["email"]] = (int) $pdo->lastInsertId();
        $summary["recipients"]["inserted"]++;
        report("recipients", "INSERTED", $profile["email"]);
    }

    $matches = [
        ["donor" => "demo.donor.one@example.test", "recipient" => "demo.recipient.one@example.test", "status" => "approved"],
        ["donor" => "demo.donor.two@example.test", "recipient" => "demo.recipient.two@example.test", "status" => "rejected"],
        ["donor" => "demo.donor.three@example.test", "recipient" => "demo.recipient.three@example.test", "status" => "pending"]
    ];
    $findMatch = $pdo->prepare(
        "SELECT id, match_status FROM matches WHERE donor_id = ? AND recipient_id = ? LIMIT 1"
    );
    $insertMatch = $pdo->prepare(
        "INSERT INTO matches (donor_id, recipient_id, match_status) VALUES (?, ?, ?)"
    );

    foreach ($matches as $match) {
        $donorId = $donorIds[$match["donor"]];
        $recipientId = $recipientIds[$match["recipient"]];
        $findMatch->execute([$donorId, $recipientId]);
        $existing = $findMatch->fetch();

        if ($existing) {
            $summary["matches"]["skipped"]++;
            report("matches", "SKIPPED", "donor $donorId / recipient $recipientId already exists as " . $existing["match_status"]);
            continue;
        }

        $insertMatch->execute([$donorId, $recipientId, $match["status"]]);
        $summary["matches"]["inserted"]++;
        report("matches", "INSERTED", "donor $donorId / recipient $recipientId / " . $match["status"]);
    }

    $notifications = [
        ["email" => "demo.recipient.one@example.test", "title" => "Demo Match Approved", "message" => "Your demo kidney match has been approved."],
        ["email" => "demo.recipient.two@example.test", "title" => "Demo Match Update", "message" => "Your demo liver match was rejected."],
        ["email" => "demo.admin@example.test", "title" => "Demo Seed Complete", "message" => "Synthetic demo data is available for review."]
    ];
    $findNotification = $pdo->prepare(
        "SELECT id FROM notifications WHERE user_id = ? AND title = ? AND message = ? LIMIT 1"
    );
    $insertNotification = $pdo->prepare(
        "INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)"
    );

    foreach ($notifications as $notification) {
        $userId = $userIds[$notification["email"]];
        $findNotification->execute([
            $userId,
            $notification["title"],
            $notification["message"]
        ]);

        if ($findNotification->fetch()) {
            $summary["notifications"]["skipped"]++;
            report("notifications", "SKIPPED", $notification["title"] . " already exists");
            continue;
        }

        $insertNotification->execute([
            $userId,
            $notification["title"],
            $notification["message"]
        ]);
        $summary["notifications"]["inserted"]++;
        report("notifications", "INSERTED", $notification["title"]);
    }

    $pdo->commit();
    echo PHP_EOL . "SEED COMPLETE" . PHP_EOL;
    foreach ($summary as $type => $counts) {
        echo strtoupper($type) . ": inserted=" . $counts["inserted"] . ", skipped=" . $counts["skipped"] . PHP_EOL;
    }
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "SEED FAILED: " . $exception->getMessage() . PHP_EOL);
    exit(1);
}
