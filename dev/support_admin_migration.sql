-- ============================================================
-- SUPPORT ADMIN MIGRATION
-- Run this AFTER Final_DB.sql to add admin reply capabilities
-- ============================================================

-- Add admin reply columns to support_tickets
ALTER TABLE support_tickets 
  ADD COLUMN admin_reply TEXT NULL AFTER status,
  ADD COLUMN admin_replied_at TIMESTAMP NULL AFTER admin_reply;

-- Add admin reply columns to support_problem_reports
ALTER TABLE support_problem_reports 
  ADD COLUMN admin_reply TEXT NULL AFTER status,
  ADD COLUMN admin_replied_at TIMESTAMP NULL AFTER admin_reply;
